<?php

namespace addons\TinyShop\services\order;

use common\components\Service;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;
use yii\web\UnprocessableEntityHttpException;
use addons\TinyShop\common\models\order\Order;
use addons\TinyShop\common\models\order\Store;

/**
 * Class StoreService
 * @package addons\TinyShop\services\order
 * @author jianyan74 <751393839@qq.com>
 */
class StoreService extends Service
{
    /**
     * @param $store
     * @param Order $order
     */
    public function create($store, Order $order)
    {
        $model = new Store();
        $model = $model->loadDefaultValues();
        $model->attributes = ArrayHelper::toArray($store);
        $model->merchant_id = $order->merchant_id;
        $model->member_id = $order->buyer_id;
        $model->order_id = $order->id;
        $model->store_id = $store->id;
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
        return Store::find()
            ->where(['order_id' => $order_id])
            ->one();
    }
}
