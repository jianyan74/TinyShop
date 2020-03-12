<?php

namespace addons\TinyShop\services\order;

use common\components\Service;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;
use addons\TinyShop\common\models\order\Order;
use addons\TinyShop\common\models\pickup\Point;
use addons\TinyShop\common\models\order\Pickup;

/**
 * 自提
 *
 * Class PickupService
 * @package addons\TinyShop\services\order
 * @author jianyan74 <751393839@qq.com>
 */
class PickupService extends Service
{
    /**
     * @param Point $point
     * @param Order $order
     */
    public function create(Point $point, Order $order)
    {
        $model = new Pickup();
        $model = $model->loadDefaultValues();
        $model->attributes = ArrayHelper::toArray($point);
        $model->merchant_id = $order->merchant_id;
        $model->member_id = $order->buyer_id;
        $model->order_id = $order->id;
        $model->pickup_id = $point->id;
        $model->pickup_code = StringHelper::randomNum('', 6);
        $model->save();
    }
}