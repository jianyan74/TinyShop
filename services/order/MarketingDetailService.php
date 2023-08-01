<?php

namespace addons\TinyShop\services\order;

use common\components\Service;
use addons\TinyShop\common\models\order\MarketingDetail;

/**
 * Class MarketingDetailService
 * @package addons\TinyShop\services\order
 * @author jianyan74 <751393839@qq.com>
 */
class MarketingDetailService extends Service
{
    /**
     * @param $order_id
     * @param array $data
     */
    public function create($order_id, array $data)
    {
        foreach ($data as $datum) {
            $model = new MarketingDetail();
            $model = $model->loadDefaultValues();
            $model->attributes = $datum;
            $model->order_id = $order_id;
            $model->save();
        }
    }
}