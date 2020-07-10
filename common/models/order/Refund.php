<?php

namespace addons\TinyShop\common\models\order;

use Yii;

/**
 * This is the model class for table "{{%addon_shop_order_refund}}".
 *
 * @property int $id id
 * @property int $app_id 操作方 1 买家 2 卖家
 * @property int $order_id 订单id
 * @property int $order_product_id 订单商品表id
 * @property int $refund_status 操作状态 流程状态(refund_status) 状态名称(refund_status_name)  操作时间1 买家申请  发起了退款申请,等待卖家处理2 等待买家退货  卖家已同意退款申请,等待买家退货3 等待卖家确认收货  买家已退货,等待卖家确认收货4 等待卖家确认退款  卖家同意退款
 * @property string $action 退款操作内容描述
 * @property int $action_member_id 操作人id
 * @property string $action_member_name 操作人姓名
 * @property int $status 状态[-1:删除;0:禁用;1启用]
 * @property int $created_at
 * @property int $updated_at
 */
class Refund extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_order_refund}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'order_product_id', 'refund_status', 'action_member_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['order_id', 'order_product_id', 'refund_status', 'action'], 'required'],
            [['action', 'action_member_name'], 'string', 'max' => 255],
            [['app_id'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'app_id' => '操作方',
            'order_id' => '订单id',
            'order_product_id' => '订单商品表id',
            'refund_status' => '操作状态 流程状态(refund_status) 状态名称(refund_status_name)  操作时间1 买家申请  发起了退款申请,等待卖家处理2 等待买家退货  卖家已同意退款申请,等待买家退货3 等待卖家确认收货  买家已退货,等待卖家确认收货4 等待卖家确认退款  卖家同意退款',
            'action' => '退款操作内容描述',
            'action_member_id' => '操作人id',
            'action_member_name' => '操作人姓名',
            'status' => '状态[-1:删除;0:禁用;1启用]',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
