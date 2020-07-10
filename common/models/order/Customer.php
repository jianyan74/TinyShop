<?php

namespace addons\TinyShop\common\models\order;

/**
 * This is the model class for table "{{%addon_shop_order_customer}}".
 *
 * @property string $id 主键id
 * @property int $merchant_id 店铺ID
 * @property int $product_id 商品id
 * @property int $order_id 订单id
 * @property string $member_id 用户id
 * @property string $order_sn 订单编号
 * @property int $order_product_id 订单项id
 * @property string $product_name 商品名称
 * @property int $sku_id skuID
 * @property string $sku_name sku名称
 * @property string $price 商品价格
 * @property string $product_picture 商品图片
 * @property int $num 购买数量
 * @property int $order_type 订单类型
 * @property string $refund_require_money 退款金额
 * @property string $refund_type 退款方式  退款退货
 * @property string $refund_reason 退款原因
 * @property int $refund_status 退款状态
 * @property int $refund_time 退款时间
 * @property string $refund_shipping_code 退款物流单号
 * @property string $refund_shipping_company 退款物流公司名称
 * @property string $refund_balance_money 订单退款余额
 * @property string $order_from 订单来源
 * @property string $user_name 买家会员名称
 * @property string $receiver_name 收货人姓名
 * @property int $receiver_province 收货人所在省
 * @property int $receiver_city 收货人所在城市
 * @property int $receiver_area 收货人所在街道
 * @property string $receiver_address 收货人详细地址
 * @property string $receiver_region_name 收货人详细地址
 * @property string $receiver_mobile 收货人的手机号码
 * @property int $payment_type 支付类型。取值范围：...
 * @property int $shipping_type 订单配送方式
 * @property string $product_money 商品总价
 * @property string $fixed_telephone 固定电话
 * @property int $status 状态[-1:删除;0:禁用;1启用]
 * @property int $created_at
 * @property int $updated_at
 */
class Customer extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_order_customer}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'product_id', 'order_id', 'member_id', 'order_product_id', 'sku_id', 'num', 'order_type', 'refund_status', 'refund_time', 'receiver_province', 'receiver_city', 'receiver_area', 'payment_type', 'shipping_type', 'status', 'created_at', 'updated_at'], 'integer'],
            [['price', 'refund_require_money', 'refund_balance_money', 'product_money'], 'number'],
            [['order_sn'], 'string', 'max' => 30],
            [['product_name'], 'string', 'max' => 200],
            [['sku_name', 'user_name', 'receiver_name', 'fixed_telephone'], 'string', 'max' => 50],
            [['product_picture', 'refund_shipping_code', 'refund_shipping_company'], 'string', 'max' => 100],
            [['refund_type', 'refund_reason', 'order_from', 'receiver_address', 'memo'], 'string', 'max' => 255],
            [['receiver_region_name'], 'string', 'max' => 200],
            [['receiver_mobile'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键id',
            'merchant_id' => '店铺ID',
            'product_id' => '商品id',
            'order_id' => '订单id',
            'member_id' => '用户id',
            'order_sn' => '订单编号',
            'order_product_id' => '订单项id',
            'product_name' => '商品名称',
            'sku_id' => 'skuID',
            'sku_name' => 'sku名称',
            'price' => '商品价格',
            'product_picture' => '商品图片',
            'num' => '购买数量',
            'order_type' => '订单类型',
            'refund_require_money' => '退款金额',
            'refund_type' => '退款方式  退款退货',
            'refund_reason' => '退款原因',
            'refund_explain' => '退款说明',
            'refund_evidence' => '退款凭证',
            'refund_status' => '退款状态',
            'refund_time' => '退款时间',
            'refund_shipping_code' => '退款物流单号',
            'refund_shipping_company' => '退款物流公司名称',
            'refund_balance_money' => '订单退款余额',
            'order_from' => '订单来源',
            'user_name' => '买家会员名称',
            'receiver_name' => '收货人姓名',
            'receiver_province' => '收货人所在省',
            'receiver_city' => '收货人所在城市',
            'receiver_area' => '收货人所在街道',
            'receiver_address' => '收货人详细地址',
            'receiver_region_name' => '收货人详细地址',
            'receiver_mobile' => '收货人的手机号码',
            'payment_type' => '支付类型。取值范围：...',
            'shipping_type' => '订单配送方式',
            'product_money' => '商品总价',
            'fixed_telephone' => '固定电话',
            'memo' => '备注',
            'status' => '状态[-1:删除;0:禁用;1启用]',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
    }
}
