<?php

namespace addons\TinyShop\common\models\order;

use Yii;

/**
 * This is the model class for table "{{%addon_tiny_shop_order_recharge}}".
 *
 * @property int $id ID
 * @property int|null $merchant_id 商户ID
 * @property int|null $store_id 店铺ID
 * @property int|null $member_id 用户ID
 * @property string $order_sn 订单编号
 * @property string|null $out_trade_no 外部交易号
 * @property float|null $price 价格
 * @property float|null $give_price 赠送金额
 * @property int|null $give_point 送积分数量（0表示不送）
 * @property int|null $give_growth 赠送成长值
 * @property string|null $give_coupon_type_ids 优惠券
 * @property int|null $pay_type 支付类型
 * @property int|null $pay_status 订单付款状态
 * @property int|null $pay_time 订单付款时间
 * @property int|null $status 状态[-1:删除;0:禁用;1启用]
 * @property int|null $created_at 创建时间
 * @property int|null $updated_at 修改时间
 */
class Recharge extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_order_recharge}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'store_id', 'member_id', 'give_point', 'give_growth', 'pay_type', 'pay_status', 'pay_time', 'status', 'created_at', 'updated_at'], 'integer'],
            [['price', 'give_price'], 'number'],
            [['give_coupon_type_ids'], 'safe'],
            [['order_sn', 'out_trade_no'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'merchant_id' => '商户ID',
            'store_id' => '店铺ID',
            'member_id' => '用户ID',
            'order_sn' => '订单编号',
            'out_trade_no' => '外部交易号',
            'price' => '价格',
            'give_price' => '赠送金额',
            'give_point' => '送积分数量（0表示不送）',
            'give_growth' => '赠送成长值',
            'give_coupon_type_ids' => '优惠券',
            'pay_type' => '支付类型',
            'pay_status' => '订单付款状态',
            'pay_time' => '订单付款时间',
            'status' => '状态[-1:删除;0:禁用;1启用]',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }
}
