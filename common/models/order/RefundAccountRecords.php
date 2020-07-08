<?php

namespace addons\TinyShop\common\models\order;

use Yii;

/**
 * This is the model class for table "{{%addon_shop_order_refund_account_records}}".
 *
 * @property int $id 主键id
 * @property int $order_id 订单id
 * @property int $order_product_id 订单项id
 * @property string $refund_trade_no 退款交易号
 * @property string $refund_money 退款金额
 * @property int $refund_way 退款方式（1：微信，2：支付宝，10：线下）
 * @property int $buyer_id 买家id
 * @property string $remark 备注
 * @property int $created_at
 * @property int $updated_at
 */
class RefundAccountRecords extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_order_refund_account_records}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'order_product_id', 'refund_trade_no', 'refund_money', 'refund_way', 'buyer_id'], 'required'],
            [['order_id', 'order_product_id', 'refund_way', 'buyer_id', 'created_at', 'updated_at'], 'integer'],
            [['refund_money'], 'number'],
            [['refund_trade_no'], 'string', 'max' => 55],
            [['remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键id',
            'order_id' => '订单id',
            'order_product_id' => '订单项id',
            'refund_trade_no' => '退款交易号',
            'refund_money' => '退款金额',
            'refund_way' => '退款方式（1：微信，2：支付宝，10：线下）',
            'buyer_id' => '买家id',
            'remark' => '备注',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
