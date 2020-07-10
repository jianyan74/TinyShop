<?php

namespace addons\TinyShop\common\models\order;

use Yii;

/**
 * This is the model class for table "{{%addon_shop_order_presell}}".
 *
 * @property int $id 订单id
 * @property string $out_trade_no 外部交易号
 * @property int $payment_type 支付类型
 * @property int $order_status 订单状态 0创建 1尾款待支付 2开始结尾款 
 * @property int $pay_time 订单付款时间
 * @property int $operator_type 操作人类型  1店铺  2用户
 * @property int $operator_id 操作人id
 * @property int $order_id 订单id
 * @property int $presell_time 预售结束时间
 * @property string $presell_money 预售金额
 * @property string $presell_pay 预售支付金额
 * @property string $platform_money 平台余额
 * @property string $point 订单消耗积分
 * @property string $point_money 订单消耗积分抵多少钱
 * @property string $presell_price 预售金单价
 * @property int $presell_delivery_type 预售发货形式 1指定时间 2支付后天数
 * @property int $presell_delivery_value 预售发货时间 按形式 
 * @property int $presell_delivery_time 预售发货具体时间（实则为结尾款时间）
 * @property int $is_full_payment 是否全款预定
 * @property int $status 状态
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class OrderPresell extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_order_presell}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['payment_type', 'order_status', 'pay_time', 'operator_type', 'operator_id', 'order_id', 'presell_time', 'presell_delivery_type', 'presell_delivery_value', 'presell_delivery_time', 'is_full_payment', 'status', 'created_at', 'updated_at'], 'integer'],
            [['presell_money', 'presell_pay', 'platform_money', 'point', 'point_money', 'presell_price'], 'number'],
            [['out_trade_no'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '订单id',
            'out_trade_no' => '外部交易号',
            'payment_type' => '支付类型',
            'order_status' => '订单状态 0创建 1尾款待支付 2开始结尾款 ',
            'pay_time' => '订单付款时间',
            'operator_type' => '操作人类型  1店铺  2用户',
            'operator_id' => '操作人id',
            'order_id' => '订单id',
            'presell_time' => '预售结束时间',
            'presell_money' => '预售金额',
            'presell_pay' => '预售支付金额',
            'platform_money' => '平台余额',
            'point' => '订单消耗积分',
            'point_money' => '订单消耗积分抵多少钱',
            'presell_price' => '预售金单价',
            'presell_delivery_type' => '预售发货形式 1指定时间 2支付后天数',
            'presell_delivery_value' => '预售发货时间 按形式 ',
            'presell_delivery_time' => '预售发货具体时间（实则为结尾款时间）',
            'is_full_payment' => '是否全款预定',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
