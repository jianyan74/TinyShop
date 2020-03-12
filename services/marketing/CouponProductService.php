<?php

namespace addons\TinyShop\services\marketing;

use Yii;
use common\components\Service;
use addons\TinyShop\common\models\marketing\CouponProduct;

/**
 * Class CouponProductService
 * @package addons\TinyShop\services\marketing
 * @author jianyan74 <751393839@qq.com>
 */
class CouponProductService extends Service
{
    /**
     * @param int $coupon_type_id
     * @param array $product_ids
     * @throws \yii\db\Exception
     */
    public function create(int $coupon_type_id, array $product_ids)
    {
        CouponProduct::deleteAll(['coupon_type_id' => $coupon_type_id]);

        $rows = [];
        foreach ($product_ids as $id) {
            $rows[] = [
                'coupon_type_id' => $coupon_type_id,
                'product_id' => $id,
            ];
        }

        $field = ['coupon_type_id', 'product_id'];
        !empty($rows) && Yii::$app->db->createCommand()->batchInsert(CouponProduct::tableName(), $field,
            $rows)->execute();
    }

    /**
     * 根据产品id获取所有优惠劵类型id
     *
     * @param $product_id
     * @return array
     */
    public function getCouponTypeIds($product_id)
    {
        return CouponProduct::find()
            ->where(['product_id' => $product_id])
            ->select('coupon_type_id')
            ->column();
    }

    /**
     * 根据优惠劵类型获取所有产品id
     *
     * @param $coupon_type_id
     * @return array
     */
    public function getProductIds($coupon_type_id)
    {
        return CouponProduct::find()
            ->where(['coupon_type_id' => $coupon_type_id])
            ->select('product_id')
            ->column();
    }
}