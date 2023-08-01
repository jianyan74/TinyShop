<?php

namespace addons\TinyShop\services\marketing;

use addons\TinyShop\common\enums\DiscountTypeEnum;
use addons\TinyShop\common\enums\RangeTypeEnum;
use addons\TinyShop\common\models\marketing\CouponTypeMap;

/**
 * Class CouponTypeMapService
 * @package addons\TinyShop\services\marketing
 * @author jianyan74 <751393839@qq.com>
 */
class CouponTypeMapService
{
    /**
     * @param $marketing_id
     * @param $marketing_type
     * @return array
     */
    public function regroup($marketing_id, $marketing_type)
    {
        $marketingCouponTypeMaps = $this->findByMarketingWithCouponType($marketing_id, $marketing_type);
        $data = [];
        foreach ($marketingCouponTypeMaps as $marketingCouponTypeMap) {
            $couponType = $marketingCouponTypeMap['couponType'];
            unset($marketingCouponTypeMap['couponType']);
            $couponType['number'] = $marketingCouponTypeMap['number'];
            $couponType['range_type'] = RangeTypeEnum::getValue($couponType['range_type']);
            $couponType['stock'] = $couponType['count'] - $couponType['get_count'];

            if ($couponType['at_least'] == 0) {
                $discount = '无门槛, ';
            } else {
                $discount = '满 ' . floatval($couponType['at_least']) . ' 元, ';
            }

            if ($couponType['discount_type'] == DiscountTypeEnum::MONEY) {
                $discount .= '减 ' . floatval($couponType['discount']) . ' 元';
            } else {
                $discount .= '打 ' . floatval($couponType['discount']) . ' 折';
            }

            $couponType['discount'] = $discount;

            $data[] = $couponType;
        }

        return $data;
    }

    /**
     * @param $marketing_id
     * @param $marketing_type
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findByMarketing($marketing_id, $marketing_type)
    {
        return CouponTypeMap::find()
            ->where([
                'marketing_id' => $marketing_id,
                'marketing_type' => $marketing_type,
            ])
            ->asArray()
            ->all();
    }

    /**
     * @param $marketing_id
     * @param $marketing_type
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findByMarketingWithCouponType($marketing_id, $marketing_type)
    {
        return CouponTypeMap::find()
            ->where([
                'marketing_id' => $marketing_id,
                'marketing_type' => $marketing_type,
            ])
            ->with(['couponType'])
            ->asArray()
            ->all();
    }

    /**
     * @param $marketing_id
     * @param $marketing_type
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findObjByMarketingWithCouponType($marketing_id, $marketing_type)
    {
        return CouponTypeMap::find()
            ->where([
                'marketing_id' => $marketing_id,
                'marketing_type' => $marketing_type,
            ])
            ->with(['couponType'])
            ->all();
    }
}
