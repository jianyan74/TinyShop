<?php

namespace addons\TinyShop\services\order;

use common\components\Service;
use common\enums\StatusEnum;
use addons\TinyShop\common\enums\OrderStatusEnum;
use addons\TinyShop\common\models\order\Action;

/**
 * Class ActionService
 * @package addons\TinyShop\services\order
 * @author jianyan74 <751393839@qq.com>
 */
class ActionService extends Service
{
    /**
     * @param $action
     * @param $order_id
     * @param $order_status
     * @param $member_id
     * @param $member_name
     */
    public function create($action, $order_id, $order_status, $member_id, $member_name)
    {
        $model = new Action();
        $model = $model->loadDefaultValues();
        $model->action = $action;
        $model->order_id = $order_id;
        $model->order_status = $order_status;
        $model->order_status_text = OrderStatusEnum::getValue($model->order_status);
        $model->member_id = $member_id;
        $model->member_name = $member_name;
        $model->save();
    }

    /**
     * @param $order_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findByOrderId($order_id)
    {
        return Action::find()
            ->where(['order_id' => $order_id])
            ->andWhere(['status' => StatusEnum::ENABLED])
            ->orderBy('id desc')
            ->all();
    }
}