<?php

namespace addons\TinyShop\services\order;

use addons\TinyShop\common\enums\RefundStatusEnum;
use common\components\Service;
use addons\TinyShop\common\models\order\Refund;
use common\enums\StatusEnum;
use yii\web\NotFoundHttpException;

/**
 * Class OrderRefundService
 * @package addons\TinyShop\services\order
 * @author jianyan74 <751393839@qq.com>
 */
class OrderRefundService extends Service
{
    /**
     * @param $action
     * @param $order_id
     * @param $order_status
     * @param $member_id
     * @param $member_name
     */
    public function create($app_id, $order_id, $order_product_id, $refund_status, $member_id, $member_name, $is_customer = false)
    {
        $model = new Refund();
        $model = $model->loadDefaultValues();
        $model->app_id = $app_id;
        $model->order_id = $order_id;
        $model->order_product_id = $order_product_id;
        $model->refund_status = $refund_status;
        $model->action = RefundStatusEnum::getValue($model->refund_status);
        $model->action_member_id = $member_id;
        $model->action_member_name = $member_name;
        $model->is_customer = $is_customer == false ? StatusEnum::DISABLED : StatusEnum::ENABLED;
        if (!$model->save()) {
            throw new NotFoundHttpException($this->getError($model));
        }
    }

    /**
     * @param $order_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findByOrderId($order_id, $is_customer)
    {
        return Refund::find()
            ->where(['order_id' => $order_id, 'is_customer' => $is_customer])
            ->andWhere(['status' => StatusEnum::ENABLED])
            ->orderBy('id desc')
            ->all();
    }

    /**
     * @param $order_product_id
     * @param $is_customer
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findByOrderProductId($order_product_id, $is_customer)
    {
        return Refund::find()
            ->where(['order_product_id' => $order_product_id, 'is_customer' => $is_customer])
            ->andWhere(['status' => StatusEnum::ENABLED])
            ->orderBy('id desc')
            ->all();
    }
}