<?php

namespace addons\TinyShop\services\common;

use common\helpers\BcHelper;
use Yii;
use yii\web\UnprocessableEntityHttpException;
use common\components\Service;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use common\models\member\Address;
use common\helpers\StringHelper;
use addons\TinyShop\common\models\common\ExpressFee;
use addons\TinyShop\common\enums\ProductShippingTypeEnum;

/**
 * Class ExpressFeeService
 * @package addons\TinyShop\services\common
 * @author jianyan74 <751393839@qq.com>
 */
class ExpressFeeService extends Service
{
    /**
     * 获取运费
     *
     * @param $defaultProducts
     * @param array $fullProductIds 包邮id数组
     * @param $company_id
     * @param Address $address
     * @return float|int
     * @throws UnprocessableEntityHttpException
     */
    public function getPrice($defaultProducts,array $fullProductIds, $company_id, $merchant_id, Address $address, $is_logistics)
    {
        // 查询用户是否选择物流
        if (!empty($is_logistics)) {
            return $this->getSameExpressSkuListFee($defaultProducts, $fullProductIds, $company_id, $address);
        }

        // 根据商品获取物流公司组别
        if (!empty($companies = $this->getSkuExpressGroup($defaultProducts, $merchant_id))) {
            $fee = 0;
            foreach ($companies as $company) {
                if (!empty($company['defaultProducts'])) {
                    $same_fee = $this->getSameExpressSkuListFee($company['defaultProducts'], $fullProductIds, $company['id'], $address);
                    if ($same_fee >= 0) {
                        $fee += $same_fee;
                    } else {
                        throw new UnprocessableEntityHttpException('找不到物流');
                    }
                }
            }

            return $fee;
        }

        throw new UnprocessableEntityHttpException('未设置物流');
    }

    /**
     * 获取相同运费模板运费情况
     *
     * @param $defaultProducts
     * @param array $fullProductIds 包邮id数组
     * @param $company_id
     * @param $address
     * @return float|int
     * @throws UnprocessableEntityHttpException
     */
    public function getSameExpressSkuListFee($defaultProducts, $fullProductIds, $company_id, $address)
    {
        $fee = 0;
        if (empty($defaultProducts)) {
            return $fee;
        }

        list ($weightProducts, $volumeProducts, $byNumProducts, $unifyProducts, $freeExpressProducts) = $this->getSkuGroup($defaultProducts, $fullProductIds);
        if (count($freeExpressProducts) === count($defaultProducts)) {
            return $fee;
        }

        if (!empty($weightProducts) || !empty($volumeProducts) || !empty($byNumProducts)) {
            $template = $this->getTemplate($company_id, $address);
            // 计算称重方式运费
            $weightProductsFee = $this->getWeightFee($template, $weightProducts);
            $fee += $weightProductsFee;
            // 计算体积方式运费
            $volumeProductsFee = $this->getVolumeFee($template, $volumeProducts);
            $fee += $volumeProductsFee;
            // 计件方式计算运费
            $byNumProductsFee = $this->getByNumFee($template, $byNumProducts);
            $fee += $byNumProductsFee;
        }

        // 统一计算邮费
        $unifyProductsFee = $this->getUnifyFee($unifyProducts);
        $fee += $unifyProductsFee;

        return $fee;
    }

    /**
     * @param array $defaultProducts
     * @param array $fullProductIds 包邮id数组
     * @return array
     */
    public function getSkuGroup(array $defaultProducts, $fullProductIds = [])
    {
        $weightProducts = [];
        $volumeProducts = [];
        $byNumProducts = [];
        $unifyProducts = [];
        $freeExpressProducts = [];

        foreach ($defaultProducts as $defaultProduct) {
            // 包邮商品
            if (in_array($defaultProduct['id'], $fullProductIds)) {
                $freeExpressProducts[] = $defaultProduct;
                continue;
            }

            // 包邮
            if ($defaultProduct['shipping_type'] == ProductShippingTypeEnum::FULL_MAIL) {
                $freeExpressProducts[] = $defaultProduct;
                continue;
            }

            // 买家承担运费
            if ($defaultProduct['shipping_type'] == ProductShippingTypeEnum::USER_PAY) {
                switch ($defaultProduct['shipping_fee_type']) {
                    case 1; // 计件
                        $byNumProducts[] = $defaultProduct;
                        break;
                    case 2; // 体积
                        $volumeProducts[] = $defaultProduct;
                        break;
                    case 3; // 重量
                        $weightProducts[] = $defaultProduct;
                        break;
                }
                continue;
            }

            // 统一运费(买家承担运费)
            if ($defaultProduct['shipping_type'] == ProductShippingTypeEnum::FIXATION) {
                $unifyProducts[] = $defaultProduct;
            }
        }

        return [
            $weightProducts,
            $volumeProducts,
            $byNumProducts,
            $unifyProducts,
            $freeExpressProducts,
        ];
    }

    /**
     * 根据地址获取运费模板
     *
     * @param $company_id
     * @param $address
     * @return array|mixed|\yii\db\ActiveRecord
     * @throws UnprocessableEntityHttpException
     */
    private function getTemplate($company_id, $address)
    {
        // 所有运费信息
        $fees = $this->findByCompanyId($company_id);
        // 检测城市是否有区概念
        $count = Yii::$app->services->provinces->getCountByPid($address['city_id']);

        $temp = [];
        $default = [];
        foreach ($fees as $v) {
            if ($v['is_default'] == StatusEnum::ENABLED) {
                $default = $v;
            }

            if ($count == 0) {
                if (!empty($v['city_ids'])) {
                    $cityIds = explode(',', $v['city_ids']);
                    in_array($address['city_id'], $cityIds) && $temp = $v;
                }
            } else {
                $areaIds = explode(',', $v['area_ids']);
                in_array($address['area_id'], $areaIds) && $temp = $v;
            }
        }

        // 如果模板为空，找到默认模板
        if (!empty($temp)) {
            return $temp;
        }

        if (!empty($default)) {
            $temp = $default;

            return $temp;
        }

        throw new UnprocessableEntityHttpException('该地址不支持配送');
    }

    /**
     * 商品邮费的sku分组
     *
     * @param $defaultProducts
     * @return array|string|\yii\db\ActiveRecord[]
     */
    public function getSkuExpressGroup($defaultProducts, $merchant_id)
    {
        // 获取所有物流公司
        if (empty($companies = Yii::$app->tinyShopService->expressCompany->getList($merchant_id))) {
            return '';
        }

        // 获取默认物流公司
        if (empty($defaultCompany = Yii::$app->tinyShopService->expressCompany->getDefault($merchant_id))) {
            $defaultCompany = $companies[0];
        }

        // 兼容统一运费
        $count = count($companies);
        $companies[$count] = [
            'id' => 0
        ];

        foreach ($companies as $key => $company) {
            $companies[$key]['defaultProducts'] = [];
            foreach ($defaultProducts as $k => $value) {
                // 买家承担运费
                if ($value['shipping_type'] == ProductShippingTypeEnum::USER_PAY) {
                    // 商品未设置物流公司
                    if ($value['shipping_fee_id'] == 0) {
                        $company['id'] == $defaultCompany['id'] && $companies[$key]['defaultProducts'][] = $value;
                    } else {
                        $company['id'] == $value['shipping_fee_id'] &&  $companies[$key]['defaultProducts'][] = $value;
                    }
                } elseif ($value['shipping_type'] == ProductShippingTypeEnum::FIXATION) {
                    $companies[$count]['defaultProducts'][] = $value;
                }
            }
        }

        return $companies;
    }

    /**
     * 计算称重方式运费总和
     *
     * @param $template
     * @param $products
     * @return float|int
     * @throws UnprocessableEntityHttpException
     */
    private function getWeightFee($template, $products)
    {
        $weight = 0;
        if (empty($products)) {
            return $weight;
        }

        // 不支持配送
        if ($template['weight_is_use'] == 0) {
            throw new UnprocessableEntityHttpException('不支持配送');
        }

        // 计算总重量
        foreach ($products as $v) {
            $weight += $v['product_weight'] * $v['number'];
        }

        if ($weight <= 0) {
            return 0;
        }

        if ($weight <= $template['weight_snum']) {
            return $template['weight_sprice'];
        }

        // 开始计算
        $ext_weight = $weight - $template['weight_snum'];
        if ($template['weight_xnum'] == 0) {
            $template['weight_xnum'] = 1;
        }
        if (($ext_weight * 100) % ($template['weight_xnum'] * 100) == 0) {
            $ext_data = $ext_weight / $template['weight_xnum'];
        } else {
            $ext_data = floor($ext_weight / $template['weight_xnum']) + 1;
        }

        return $template['weight_sprice'] + $ext_data * $template['weight_xprice'];
    }

    /**
     * 计算体积方式运费总和
     *
     * @param $template
     * @param $products
     * @return float|int
     * @throws UnprocessableEntityHttpException
     */
    private function getVolumeFee($template, $products)
    {
        $volume = 0;

        if (empty($products)) {
            return 0;
        }

        if ($template['volume_is_use'] == 0) {
            throw new UnprocessableEntityHttpException('不支持配送');
        }

        foreach ($products as $k => $v) {
            // 计算总重量
            $volume += $v['product_volume'] * $v['number'];
        }

        if ($volume <= 0) {
            return 0;
        }

        if ($volume <= $template['volume_snum']) {
            return $template['volume_sprice'];
        } else {
            $ext_volume = $volume - $template['volume_snum'];

            if ($template['volume_xnum'] == 0) {
                $template['volume_xnum'] = 1;
            }

            if (($ext_volume * 100) % ($template['volume_xnum'] * 100) == 0) {
                $ext_data = $ext_volume / $template['volume_xnum'];
            } else {
                $ext_data = floor($ext_volume / $template['weight_xnum']) + 1;
            }

            return $template['volume_sprice'] + $ext_data * $template['volume_xprice'];
        }
    }

    /**
     * 计算计件方式运费总和
     *
     * @param $template
     * @param $products
     * @return float|int
     * @throws UnprocessableEntityHttpException
     */
    private function getByNumFee($template, $products)
    {
        $num = 0;
        if (empty($products)) {
            return 0;
        }

        if ($template['bynum_is_use'] == 0) {
            throw new UnprocessableEntityHttpException('不支持配送');
        }

        foreach ($products as $k => $v) {
            // 计算总数量
            $num += $v['number'];
        }

        if ($num <= 0) {
            return 0;
        }

        if ($num <= $template['bynum_snum']) {
            return $template['bynum_sprice'];
        } else {
            $ext_num = $num - $template['bynum_snum'];
            if ($template['bynum_xnum'] == 0) {
                $template['bynum_xnum'] = 1;
            }
            if ($ext_num % $template['bynum_xnum'] == 0) {
                $ext_data = $ext_num / $template['bynum_xnum'];
            } else {
                $ext_data = floor($ext_num / $template['bynum_xnum']) + 1;
            }

            return $template['bynum_sprice'] + $ext_data * $template['bynum_xprice'];
        }
    }

    /**
     * 统一邮费
     *
     * @param $products
     * @return int|void
     */
    private function getUnifyFee($products)
    {
        $num = 0;
        if (empty($products)) {
            return 0;
        }

        foreach ($products as $v) {
            $num = BcHelper::add($num, $v['shipping_fee']);
        }

        if ($num <= 0) {
            return 0;
        }

        return $num;
    }

    /**
     * 获取不可选数据
     *
     * @param $company_id
     * @return array
     */
    public function getNotChoose($company_id)
    {
        $models = ExpressFee::find()
            ->where(['status' => StatusEnum::ENABLED, 'is_default' => 0, 'company_id' => $company_id])
            ->select(['province_ids', 'city_ids', 'area_ids'])
            ->asArray()
            ->all();

        $allProvinceIds = $allCityIds = $allAreaIds = [];
        foreach ($models as $model) {
            if (!empty($province = StringHelper::parseAttr($model['province_ids']))) {
                $allProvinceIds = ArrayHelper::merge($allProvinceIds, $province);
            }

            if (!empty($city = StringHelper::parseAttr($model['city_ids']))) {
                $allCityIds = ArrayHelper::merge($allCityIds, $city);
            }

            if (!empty($area = StringHelper::parseAttr($model['area_ids']))) {
                $allAreaIds = ArrayHelper::merge($allAreaIds, $area);
            }
        }

        return [$allProvinceIds, $allCityIds, $allAreaIds];
    }

    /**
     * 判断是否默认的物流
     *
     * @param $company_id
     * @return array|null|\yii\db\ActiveRecord
     */
    public function findDefaultFee($company_id)
    {
        return ExpressFee::find()
            ->where(['status' => StatusEnum::ENABLED, 'is_default' => true])
            ->andWhere(['company_id' => $company_id])
            ->one();
    }

    /**
     * 获取列表
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findByCompanyId($company_id)
    {
        return ExpressFee::find()
            ->where(['company_id' => $company_id, 'status' => StatusEnum::ENABLED])
            ->asArray()
            ->all();
    }
}
