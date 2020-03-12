<?php

namespace addons\TinyShop\services\product;

use yii\web\UnprocessableEntityHttpException;
use common\components\Service;
use common\helpers\BcHelper;
use addons\TinyShop\common\enums\PreferentialTypeEnum;
use addons\TinyShop\common\models\product\LadderPreferential;

/**
 * Class LadderPreferentialService
 * @package addons\TinyShop\services\product
 * @author jianyan74 <751393839@qq.com>
 */
class LadderPreferentialService extends Service
{
    /**
     * @param array $data
     * @param int $product_id
     * @param int $product_max_buy 最大可购买数量
     * @throws UnprocessableEntityHttpException
     */
    public function createByProductId(array $data, $product_id, $product_max_buy)
    {
        LadderPreferential::deleteAll(['product_id' => $product_id]);

        foreach ($data as $datum) {
            if ($product_max_buy > 0  && $product_max_buy < $datum['quantity']) {
                throw new UnprocessableEntityHttpException('阶梯优惠数量不可超出限购数量');
            }

            if (!empty($datum['quantity']) && !empty($datum['price'])) {
                $model = new LadderPreferential();
                $model->product_id = $product_id;
                $model->attributes = $datum;

                if (!$model->save()) {
                    throw new UnprocessableEntityHttpException('阶梯优惠' . $this->getError($model));
                }
            }
        }
    }

    /**
     * 计算阶梯金额
     *
     * @param $data
     * @param $total_num
     * @param $price
     * @return array
     */
    public function getPrice($data, $total_num, $price)
    {
        if ($price <= 0) {
            return [0, 0, []];
        }

        $total_price = BcHelper::mul($total_num, $price);
        if (empty($data)) {
            return [$total_price, $price, []];
        }

        foreach ($data as $datum) {
            if ($datum['quantity'] <= $total_num) {
                // 直接扣减
                if ($datum['type'] == PreferentialTypeEnum::MONEY) {
                    // 扣减金额
                    $deduction = $datum['price'];
                    // 单个金额 = 金额 - 扣减费用
                    $price = BcHelper::sub($price, $deduction);
                } else {
                    // 折扣
                    $discount = $datum['price'];
                    // 单个金额 = (金额 * 折扣) / 100
                    $price = BcHelper::div(BcHelper::mul($price, $discount), 100);
                }

                // 扣的太多的情况下
                if ($price <= 0) {
                    return [0, 0, $datum];
                }

                // 总金额 = 金额 * 数量
                $total_price = BcHelper::mul($price, $total_num);
                return [$total_price, $price, $datum];
            }
        }

        return [$total_price, $price, []];
    }
}