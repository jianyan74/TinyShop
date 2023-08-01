<?php

namespace addons\TinyShop\services\marketing;

use addons\TinyShop\common\models\marketing\MarketingStat;

/**
 * Class MarketingStatService
 * @package addons\TinyShop\services\marketing
 * @author jianyan74 <751393839@qq.com>
 */
class MarketingStatService
{
    /**
     * @param $marketing_id
     * @param $marketing_type
     * @return void
     */
    public function create($marketing_id, $marketing_type)
    {
        $model = new MarketingStat();
        $model = $model->loadDefaultValues();
        $model->marketing_id = $marketing_id;
        $model->marketing_type = $marketing_type;
        $model->save();
    }

    /**
     * @param MarketingStat $marketingStat
     * @return void
     */
    public function updateStat(MarketingStat $marketingStat)
    {
        MarketingStat::updateAllCounters([
            'total_customer_num' => $marketingStat->total_customer_num,
            'new_customer_num' => $marketingStat->new_customer_num,
            'old_customer_num' => $marketingStat->old_customer_num,
            'pay_money' => $marketingStat->pay_money,
            'order_count' => 1,
            'product_count' => $marketingStat->product_count,
            'discount_money' => $marketingStat->discount_money,
        ], [
            'marketing_id' => $marketingStat->marketing_id,
            'marketing_type' => $marketingStat->marketing_type,
        ]);
    }
}
