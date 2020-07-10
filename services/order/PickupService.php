<?php

namespace addons\TinyShop\services\order;

use common\components\Service;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;
use addons\TinyShop\common\models\order\Order;
use addons\TinyShop\common\models\pickup\Point;
use addons\TinyShop\common\models\order\Pickup;
use yii\web\UnprocessableEntityHttpException;

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
        $model->pickup_code = StringHelper::random(6, true);
        if (!$model->save()) {
            throw new UnprocessableEntityHttpException($this->getError($model));
        }

        return $model;
    }

    /**
     * @param $order_id
     * @return array|null|\yii\db\ActiveRecord|Order
     */
    public function findById($order_id)
    {
        return Pickup::find()
            ->where(['order_id' => $order_id])
            ->one();
    }
}