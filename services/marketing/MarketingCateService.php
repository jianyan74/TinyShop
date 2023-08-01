<?php

namespace addons\TinyShop\services\marketing;

use common\enums\StatusEnum;
use addons\TinyShop\common\enums\MarketingEnum;
use addons\TinyShop\common\models\marketing\MarketingCate;

/**
 * Class MarketingCateService
 * @package addons\TinyShop\services\marketing
 * @author jianyan74 <751393839@qq.com>
 */
class MarketingCateService
{
    /**
     * @param $product_id
     * @param $merchant_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getCanReceiveCouponByProductId($cate_ids, $merchant_id)
    {
        return MarketingCate::find()
            ->select(['marketing_id', 'marketing_type'])
            ->where(['in', 'cate_id', $cate_ids])
            ->andWhere(['in','merchant_id', [0, $merchant_id]])
            ->andWhere(['in', 'marketing_type', [MarketingEnum::COUPON_IN, MarketingEnum::COUPON_NOT_IN]])
            ->andWhere(['<', 'start_time', time()])
            ->andWhere(['>', 'end_time', time()])
            ->andWhere(['status' => StatusEnum::ENABLED])
            ->asArray()
            ->all();
    }

    /**
     * 删除
     *
     * @param $marketing_id
     * @param array|string $marketing_type
     */
    public function delByMarketing($marketing_id, $marketing_type)
    {
        if (is_array($marketing_type)) {
            MarketingCate::deleteAll([
                'and',
                ['marketing_id' => $marketing_id],
                ['in', 'marketing_type', $marketing_type],
            ]);
        } else {
            MarketingCate::deleteAll(['marketing_id' => $marketing_id, 'marketing_type' => $marketing_type]);
        }
    }

    /**
     * @param $marketing_id
     * @param $marketing_type
     * @return array
     */
    public function getCateIdsByMarketing($marketing_id, $marketing_type)
    {
        return MarketingCate::find()
            ->select(['cate_id'])
            ->where(['marketing_id' => $marketing_id, 'marketing_type' => $marketing_type])
            ->andWhere(['status' => StatusEnum::ENABLED])
            ->asArray()
            ->column();
    }
}
