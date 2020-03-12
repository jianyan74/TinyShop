<?php

namespace addons\TinyShop\common\models\order;

use Yii;

/**
 * This is the model class for table "{{%addon_shop_order_action}}".
 *
 * @property int $id 动作id
 * @property int $order_id 订单id
 * @property string $action 动作内容
 * @property int $member_id 操作人id
 * @property string $member_name 操作人
 * @property int $order_status 订单状态
 * @property string $order_status_text 订单状态名称
 * @property int $status 状态[-1:删除;0:禁用;1启用]
 * @property int $created_at
 * @property int $updated_at
 */
class Action extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_order_action}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'order_status'], 'required'],
            [['order_id', 'member_id', 'order_status', 'status', 'created_at', 'updated_at'], 'integer'],
            [['action', 'order_status_text'], 'string', 'max' => 200],
            [['member_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => '订单',
            'action' => '行为',
            'member_id' => 'Member ID',
            'member_name' => 'Member Name',
            'order_status' => 'Order Status',
            'order_status_text' => 'Order Status Text',
            'status' => '状态',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
