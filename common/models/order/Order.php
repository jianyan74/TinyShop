<?php

namespace addons\TinyShop\common\models\order;

use addons\TinyShop\common\models\marketing\Coupon;
use common\helpers\AddonHelper;
use common\helpers\BcHelper;
use common\helpers\RegularHelper;
use common\models\member\Member;
use addons\TinyShop\common\enums\OrderStatusEnum;
use addons\TinyShop\common\models\express\Company;
use common\traits\HasOneMerchant;

/**
 * This is the model class for table "{{%addon_shop_order}}".
 *
 * @property string $id 订单id
 * @property string $merchant_id 商户id
 * @property string $merchant_name 商户店铺名称
 * @property string $order_sn 订单编号
 * @property string $out_trade_no 外部交易号
 * @property int $order_type 订单类型
 * @property int $payment_type 支付类型。取值范围： WEIXIN (微信自有支付)  WEIXIN_DAIXIAO (微信代销支付)  ALIPAY (支付宝支付)
 * @property int $shipping_type 订单配送方式
 * @property string $order_from 订单来源
 * @property int $buyer_id 买家id
 * @property string $user_name 买家会员名称
 * @property string $buyer_ip 买家ip
 * @property string $buyer_message 买家附言
 * @property string $buyer_invoice 买家发票信息
 * @property string $receiver_mobile 收货人的手机号码
 * @property int $receiver_province 收货人所在省
 * @property int $receiver_city 收货人所在城市
 * @property int $receiver_area 收货人所在街道
 * @property string $receiver_address 收货人详细地址
 * @property string $receiver_region_name 收货人详细地址
 * @property string $receiver_zip 收货人邮编
 * @property string $receiver_name 收货人姓名
 * @property int $seller_star 卖家对订单的标注星标
 * @property string $seller_memo 卖家对订单的备注
 * @property int $consign_time_adjust 卖家延迟发货时间
 * @property double $product_money 商品总价
 * @property string $order_money 订单总价
 * @property int $point 订单消耗积分
 * @property int $point_money 订单消耗积分抵多少钱
 * @property int $coupon_money 订单代金券支付金额
 * @property int $coupon_id 订单代金券id
 * @property string $user_money 订单余额支付金额
 * @property string $marketing_money 订单优惠活动金额
 * @property double $shipping_money 订单运费
 * @property string $pay_money 订单实付金额
 * @property string $refund_money 订单退款金额
 * @property string $coin_money 购物币金额
 * @property int $give_point 订单赠送积分
 * @property string $give_coin 订单成功之后返购物币
 * @property int $order_status 订单状态
 * @property int $pay_status 订单付款状态
 * @property int $shipping_status 订单配送状态
 * @property int $review_status 订单评价状态
 * @property int $feedback_status 订单维权状态
 * @property int $is_evaluate 是否评价 0为未评价 1为已评价 2为已追评
 * @property int $tax_money
 * @property int $company_id 配送物流公司ID
 * @property int $give_point_type 积分返还类型 1 订单完成  2 订单收货 3  支付订单
 * @property int $pay_time 订单付款时间
 * @property int $product_count 商品总数量
 * @property int $shipping_time 买家要求配送时间
 * @property int $sign_time 买家签收时间
 * @property int $consign_time 卖家发货时间
 * @property int $finish_time 订单完成时间
 * @property int $operator_type 操作人类型  1店铺  2用户
 * @property int $operator_id 操作人id
 * @property string $refund_balance_money 订单退款余额
 * @property string $fixed_telephone 固定电话
 * @property string $distribution_time_out 配送时间段
 * @property int $status 状态[-1:删除;0:禁用;1启用]
 * @property int $created_at
 * @property int $updated_at
 */
class Order extends \common\models\base\BaseModel
{
    use HasOneMerchant;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_order}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['receiver_name', 'receiver_mobile', 'receiver_address', 'receiver_province', 'receiver_city', 'receiver_area'], 'required', 'on' => 'address'],
            ['receiver_mobile', 'match', 'pattern' => RegularHelper::mobile(), 'message' => '不是一个有效的手机号码'],
            [['close_time', 'marketing_id', 'wholesale_id', 'is_virtual', 'invoice_id', 'merchant_id', 'order_type', 'payment_type', 'shipping_type', 'buyer_id', 'receiver_province', 'receiver_city', 'receiver_area', 'seller_star', 'consign_time_adjust', 'point', 'coupon_id', 'give_point', 'order_status', 'pay_status', 'shipping_status', 'review_status', 'feedback_status', 'is_evaluate', 'company_id', 'give_point_type', 'pay_time', 'shipping_time', 'sign_time', 'consign_time', 'finish_time', 'operator_type', 'operator_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['product_money', 'user_platform_money', 'product_original_money', 'order_money', 'point_money', 'coupon_money', 'user_money', 'marketing_money', 'shipping_money', 'pay_money', 'refund_money', 'coin_money', 'give_coin', 'tax_money', 'refund_balance_money', 'product_count'], 'number'],
            [['merchant_name', 'order_sn', 'out_trade_no', 'product_virtual_group'], 'string', 'max' => 100],
            [['order_from', 'buyer_message', 'buyer_invoice', 'receiver_address', 'receiver_region_name', 'company_name'], 'string', 'max' => 200],
            [['user_name', 'receiver_name', 'fixed_telephone', 'distribution_time_out', 'promo_code', 'marketing_type'], 'string', 'max' => 50],
            [['buyer_ip'], 'string', 'max' => 20],
            [['receiver_mobile'], 'string', 'max' => 11],
            [['receiver_zip'], 'string', 'max' => 10],
            [['seller_memo'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'merchant_id' => 'Merchant ID',
            'merchant_name' => '商家名称',
            'order_sn' => '订单编号',
            'out_trade_no' => '支付编号',
            'order_type' => '订单类型',
            'payment_type' => '支付类型',
            'shipping_type' => 'Shipping Type',
            'order_from' => 'Order From',
            'buyer_id' => 'Buyer ID',
            'marketing_id' => '营销ID',
            'marketing_type' => '营销类型',
            'user_name' => 'User Name',
            'buyer_ip' => 'Buyer Ip',
            'buyer_message' => 'Buyer Message',
            'buyer_invoice' => 'Buyer Invoice',
            'receiver_mobile' => '收货人手机号',
            'receiver_province' => '省',
            'receiver_city' => '市',
            'receiver_area' => '区',
            'receiver_address' => '详细地址',
            'receiver_region_name' => 'Receiver Region Name',
            'receiver_zip' => '收货人邮编',
            'receiver_name' => '收货人',
            'product_count' => '产品数量',
            'seller_star' => 'Seller Star',
            'seller_memo' => '备注',
            'consign_time_adjust' => 'Consign Time Adjust',
            'product_money' => '产品金额',
            'product_original_money' => 'product_original_money',
            'product_virtual_group' => '虚拟产品类别',
            'order_money' => '订单金额',
            'point' => '积分',
            'point_money' => '积分抵扣金额',
            'coupon_money' => '优惠券金额',
            'coupon_id' => '优惠券 ID',
            'user_money' => '使用余额',
            'user_platform_money' => '平台余额支付',
            'wholesale_id' => '拼团id',
            'marketing_money' => 'Promotion Money',
            'shipping_money' => 'Shipping Money',
            'pay_money' => 'Pay Money',
            'refund_money' => 'Refund Money',
            'coin_money' => 'Coin Money',
            'give_point' => 'Give Point',
            'give_coin' => 'Give Coin',
            'order_status' => '订单状态',
            'pay_status' => '支付状态',
            'shipping_status' => '下单状态',
            'review_status' => 'Review Status',
            'feedback_status' => 'Feedback Status',
            'is_evaluate' => 'Is Evaluate',
            'tax_money' => '开票金额',
            'company_id' => '物流公司',
            'company_name' => '物流公司名称',
            'give_point_type' => '赠送积分类型',
            'pay_time' => '支付时间',
            'shipping_time' => '下单时间',
            'sign_time' => '签收时间',
            'consign_time' => '托运时间',
            'finish_time' => '完成时间',
            'operator_type' => 'Operator Type',
            'operator_id' => 'Operator ID',
            'refund_balance_money' => 'Refund Balance Money',
            'fixed_telephone' => 'Fixed Telephone',
            'distribution_time_out' => 'Distribution Time Out',
            'promo_code' => '推广码',
            'status' => '状态',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['address'] = array_keys($this->attributeLabels());
        return $scenarios;
    }

    /**
     * 用户信息
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Member::class, ['id' => 'buyer_id']);
    }

    /**
     * 用户信息
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBaseMember()
    {
        return $this->hasOne(Member::class, ['id' => 'buyer_id'])->select(['id', 'nickname', 'head_portrait']);
    }

    /**
     * 物流公司
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::class, ['id' => 'company_id']);
    }

    /**
     * 行为记录
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAction()
    {
        return $this->hasMany(Action::class, ['order_id' => 'id']);
    }

    /**
     * 订单发票
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInvoice()
    {
        return $this->hasOne(Invoice::class, ['order_id' => 'id']);
    }

    /**
     * 订单优惠券
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCoupon()
    {
        return $this->hasOne(Coupon::class, ['use_order_id' => 'id'])->select(['id', 'use_order_id', 'title']);
    }

    /**
     * 订单自提点
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPickup()
    {
        return $this->hasOne(Pickup::class, ['order_id' => 'id']);
    }

    /**
     * 订单产品
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasMany(OrderProduct::class, ['order_id' => 'id']);
    }

    /**
     * 营销
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMarketingDetail()
    {
        return $this->hasMany(ProductMarketingDetail::class, ['order_id' => 'id']);
    }

    /**
     * 订单产品物流配送
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductExpress()
    {
        return $this->hasMany(ProductExpress::class, ['order_id' => 'id']);
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        // 未支付/未付款
        if ($this->isNewRecord || $this->order_status == OrderStatusEnum::NOT_PAY) {
            // 支付金额为产品金额
            $this->pay_money = $this->product_money;

            // 发票(税收)
            if (!empty($this->invoice_id)) {
                $config = AddonHelper::getConfig();
                $order_invoice_tax = $config['order_invoice_tax'] ?? 0;
                $order_invoice_tax = $order_invoice_tax > 0 ? BcHelper::div($order_invoice_tax, 100, 4) : 0;
                $this->tax_money = BcHelper::mul($this->pay_money, $order_invoice_tax);
                $this->pay_money += $this->tax_money;
            }

            // 实付增加运费
            $this->pay_money += $this->shipping_money;
            // 订单总金额
            $this->order_money = $this->product_money + $this->shipping_money + $this->coupon_money + $this->point_money;
            !$this->isNewRecord && Invoice::updateAll(['tax_money' => $this->tax_money], ['order_id' => $this->id]);
        }

        // 修改订单产品状态
        if (!$this->isNewRecord && $this->order_status != $this->oldAttributes['order_status']) {
            OrderProduct::updateAll(
                ['order_status' => $this->order_status],
                [
                    'order_status' => $this->oldAttributes['order_status'],
                    'order_id' => $this->id,
                ]
            );
        }

        return parent::beforeSave($insert);
    }
}
