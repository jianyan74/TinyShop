<?php

namespace addons\TinyShop\common\components;

use Yii;
use yii\helpers\Json;
use yii\web\UnprocessableEntityHttpException;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;
use addons\TinyShop\common\enums\ShippingTypeEnum;
use addons\TinyShop\common\models\order\OrderProduct;
use addons\TinyShop\common\forms\PreviewForm;
use addons\TinyShop\common\traits\AutoCalculatePriceTrait;
use addons\TinyShop\common\enums\ProductShippingTypeEnum;

/**
 * Interface InitOrderDataInterface
 * @package addons\TinyShop\common\components\purchase
 */
abstract class InitOrderDataInterface
{
    use AutoCalculatePriceTrait;

    /**
     * 创建记录
     *
     * @var bool
     */
    public $isNewRecord = false;

    /**
     * 执行
     *
     * @param PreviewForm $form
     * @return mixed
     */
    abstract public function execute(PreviewForm $form): PreviewForm;

    /**
     * 下单类型
     *
     * @return string
     */
    abstract public static function getType(): string;

    /**
     * 触发商品自带营销
     *
     * 例如：会员折扣、限时折扣、阶梯优惠等
     *
     * @param PreviewForm $previewForm
     * @param string $type 下单类型
     * @param bool $create 是否创建订单
     * @return PreviewForm
     * @throws UnprocessableEntityHttpException
     */
    public function afterExecute(PreviewForm $previewForm, string $type): PreviewForm
    {
        // 重组默认商品信息
        $defaultProducts = ArrayHelper::arrayKey($previewForm->defaultProducts, 'id');
        $orderProducts = $previewForm->orderProducts;

        $groupOrderProducts = [];
        /** @var OrderProduct $item 商品重新归类组别 */
        foreach ($orderProducts as $item) {
            if (!isset($groupOrderProducts[$item->product_id])) {
                $groupOrderProducts[$item->product_id] = [];
                $groupOrderProducts[$item->product_id]['product_id'] = $item->product_id;
                $groupOrderProducts[$item->product_id]['product_money'] = 0;
                $groupOrderProducts[$item->product_id]['count'] = 0;
                $groupOrderProducts[$item->product_id]['max_use_point'] = 0;
                $groupOrderProducts[$item->product_id]['name'] = $item->product_name;
                $groupOrderProducts[$item->product_id]['merchant_id'] = $item->merchant_id;
                $groupOrderProducts[$item->product_id]['products'] = [];
                $groupOrderProducts[$item->product_id]['cateIds'] = []; // 商家分类
                $groupOrderProducts[$item->product_id]['platformCateIds'] = []; // 平台分类
                $groupOrderProducts[$item->product_id]['allCateIds'] = []; // 商家分类(包含上级)
                $groupOrderProducts[$item->product_id]['allPlatformCateIds'] = []; // 商家分类(包含上级)

                // 记录所有产品ID 和 产品对应的所属分类
                $previewForm->productIds[] = $item->product_id;
                foreach ($defaultProducts[$item->product_id]['cateMap'] as $cateMap) {
                    if ($cateMap['merchant_id'] == 0) {
                        $previewForm->platformCateIds[] = $cateMap['cate_id'];
                        $groupOrderProducts[$item->product_id]['platformCateIds'][] = $cateMap['cate_id'];
                    } else {
                        $previewForm->cateIds[] = $cateMap['cate_id'];
                        $groupOrderProducts[$item->product_id]['cateIds'][] = $cateMap['cate_id'];
                    }
                }
            }

            $groupOrderProducts[$item->product_id]['product_money'] += $item->product_money;
            $groupOrderProducts[$item->product_id]['max_use_point'] += $defaultProducts[$item->product_id]['max_use_point'] * $item->num;
            $groupOrderProducts[$item->product_id]['count'] += $item->num;
            $groupOrderProducts[$item->product_id]['products'][] = $item;

            // 计算运费
            if (in_array($defaultProducts[$item->product_id]['shipping_type'], [ProductShippingTypeEnum::USER_PAY, ProductShippingTypeEnum::FIXATION])) {
                $previewForm->freight = true;
            }
        }

        // 获取全部分类(包含上级)
        $cateIds = Yii::$app->tinyShopService->productCate->getParentIds(array_merge($previewForm->cateIds, $previewForm->platformCateIds));
        // 写入组别
        $previewForm->groupOrderProducts = $groupOrderProducts;
        // 重新计算价格
        $previewForm = $this->calculatePrice($previewForm);
        // 重新获取组别
        $groupOrderProducts = $previewForm->groupOrderProducts;
        foreach ($groupOrderProducts as $product_id => &$groupOrderProduct) {
            $defaultProduct = $defaultProducts[$product_id];
            // 创建订单校验
            if ($this->isNewRecord == true) {
                // 单次最少购买
                if ($defaultProduct['min_buy'] > 0 && $groupOrderProduct['count'] < $defaultProduct['min_buy']) {
                    throw new UnprocessableEntityHttpException(StringHelper::textNewLine($groupOrderProduct['name'], 10, 1)[0] . ' 最少购买数量为 ' . $defaultProduct['min_buy']);
                }

                // 最多购买
                $myMaxBuy = $defaultProduct['myGet']['all_num'] ?? 0;
                if ($defaultProduct['max_buy'] > 0 && (($myMaxBuy + $groupOrderProduct['count']) > $defaultProduct['max_buy'])) {
                    throw new UnprocessableEntityHttpException(StringHelper::textNewLine($groupOrderProduct['name'], 10, 1)[0] . ' 最多可购买数量为 ' . $defaultProduct['max_buy']);
                }

                // 配送方式
                if (
                    !empty($deliveryType = Json::decode($defaultProduct['delivery_type'])) &&
                    !in_array($previewForm->shipping_type, $deliveryType)
                ) {
                    throw new UnprocessableEntityHttpException(StringHelper::textNewLine($groupOrderProduct['name'], 10, 1)[0] . '不支持' . ShippingTypeEnum::getValue($previewForm->shipping_type));
                }
            }

            //-------------------------- 分类重组 -------------------------- //
            foreach ($groupOrderProduct['cateIds'] as $cateId) {
                if (isset($cateIds[$cateId])) {
                    $groupOrderProduct['allCateIds'] = array_merge($groupOrderProduct['allCateIds'], $cateIds[$cateId]);
                }
            }
            foreach ($groupOrderProduct['platformCateIds'] as $cateId) {
                if (isset($cateIds[$cateId])) {
                    $groupOrderProduct['allPlatformCateIds'] = array_merge($groupOrderProduct['allPlatformCateIds'], $cateIds[$cateId]);
                }
            }
        }

        $previewForm->groupOrderProducts = $groupOrderProducts;

        return $previewForm;
    }
}
