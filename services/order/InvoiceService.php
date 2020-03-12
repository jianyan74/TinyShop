<?php

namespace addons\TinyShop\services\order;

use common\components\Service;
use common\helpers\ArrayHelper;
use addons\TinyShop\common\models\order\Invoice;
use addons\TinyShop\common\models\order\Order;

/**
 * Class InvoiceService
 * @package addons\TinyShop\services\order
 * @author jianyan74 <751393839@qq.com>
 */
class InvoiceService extends Service
{
    /**
     * @param Order $order
     * @param \common\models\member\Invoice $invoice
     * @param $content
     */
    public function create(Order $order, \common\models\member\Invoice $invoice, $content)
    {
        $model = new Invoice();
        $model->attributes = ArrayHelper::toArray($invoice);
        $model->order_id = $order->id;
        $model->merchant_id = $order->merchant_id;
        $model->order_sn = $order->order_sn;
        $model->tax_money = $order->tax_money;
        $model->user_name = $order->user_name;
        $model->content = $content;
        $model->save();

        return $model;
    }

    /**
     * @param $order_id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findByOrderId($order_id)
    {
        return Invoice::find()
            ->where(['order_id' => $order_id])
            ->one();
    }
}