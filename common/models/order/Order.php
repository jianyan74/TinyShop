<?php

namespace addons\TinyShop\common\models\order;

use Yii;
use common\enums\StatusEnum;
use common\helpers\BcHelper;
use common\traits\HasOneMerchant;
use common\models\extend\PayLog;
use common\models\common\Provinces;
use common\models\member\Member;
use addons\TinyShop\common\enums\OrderStatusEnum;
use addons\TinyShop\common\models\common\ExpressCompany;
use addons\TinyShop\common\models\marketing\Coupon;
use addons\TinyShop\common\models\marketing\WholesaleRecord;

/**
 * This is the model class for table "{{%addon_tiny_shop_order}}".
 *
 * @property int $id 订单id
 * @property int $merchant_id 商户id
 * @property string|null $merchant_title 商户店铺名称
 * @property string|null $order_sn 订单编号
 * @property string|null $unite_no 订单关联编号(批量支付)
 * @property string|null $order_from 订单来源
 * @property string|null $out_trade_no 外部交易号
 * @property int|null $order_type 订单类型
 * @property int|null $pay_type 支付类型
 * @property int|null $shipping_type 订单配送方式
 * @property int|null $buyer_id 买家id
 * @property string|null $buyer_nickname 买家会员名称
 * @property string|null $buyer_ip 买家ip
 * @property string|null $buyer_message 买家附言
 * @property int|null $receiver_id 收货地址ID
 * @property string|null $receiver_mobile 收货人的手机号码
 * @property int|null $receiver_province_id 收货人所在省
 * @property int|null $receiver_city_id 收货人所在城市
 * @property int|null $receiver_area_id 收货人所在街道
 * @property string|null $receiver_name 收货人详细地址
 * @property string|null $receiver_details 收货人详细地址
 * @property string|null $receiver_zip 收货人邮编
 * @property string|null $receiver_realname 收货人姓名
 * @property string|null $receiver_longitude 收货人经度
 * @property string|null $receiver_latitude 收货人纬度
 * @property int|null $seller_star 卖家对订单的标注星标
 * @property string|null $seller_memo 卖家对订单的备注
 * @property int|null $consign_time_adjust 卖家延迟发货时间
 * @property float|null $shipping_money 订单运费
 * @property float|null $product_money 商品优惠后总价
 * @property float|null $product_original_money 商品原本总价
 * @property float|null $product_profit_price 商品利润
 * @property int|null $product_type 商品类型
 * @property int|null $product_count 订单数量
 * @property float|null $order_money 订单总价
 * @property float|null $pay_money 订单实付金额
 * @property float|null $final_money 预售尾款
 * @property int|null $point 订单消耗积分
 * @property int $marketing_id 营销活动id
 * @property string $marketing_type 营销活动类型
 * @property int $marketing_product_id 营销活动产品id
 * @property int $wholesale_record_id 拼团记录ID
 * @property float|null $marketing_money 订单优惠活动金额
 * @property int|null $give_point 订单赠送积分
 * @property int|null $give_growth 赠送成长值
 * @property float|null $give_coin 订单成功之后返购物币
 * @property int|null $order_status 订单状态
 * @property int|null $pay_status 订单付款状态
 * @property int|null $shipping_status 订单配送状态
 * @property int|null $feedback_status 订单维权状态
 * @property int|null $is_evaluate 是否评价 0为未评价 1为已评价 2为已追评
 * @property float|null $tax_money 税费
 * @property int|null $store_id 门店id
 * @property int|null $invoice_id 发票id
 * @property int|null $express_company_id 物流公司
 * @property int|null $give_point_type 积分返还类型 1 订单完成  2 订单收货 3  支付订单
 * @property int|null $give_growth_type 成长值返还类型 1 订单完成  2 订单收货 3  支付订单
 * @property int|null $caballero_member_id 骑手用户id
 * @property int|null $pay_time 订单付款时间
 * @property int|null $receiving_time 骑手接单时间
 * @property int|null $consign_time 卖家发货时间
 * @property int|null $sign_time 买家签收时间
 * @property int|null $finish_time 订单完成时间
 * @property int|null $close_time 关闭的时间
 * @property int|null $auto_sign_time 自动签收时间
 * @property int|null $auto_finish_time 自动完成时间
 * @property int|null $auto_evaluate_time 自动评价时间
 * @property string|null $fixed_telephone 固定电话
 * @property string|null $distribution_time_out 配送时间段
 * @property int|null $subscribe_shipping_start_time 预约配送开始时间
 * @property int|null $subscribe_shipping_end_time 预约配送结束时间
 * @property int|null $is_new_member 是否新顾客
 * @property int $is_print 已打印 0未打印1已打印
 * @property int|null $is_oversold 是否超卖
 * @property float|null $refund_money 退款金额
 * @property int|null $refund_num 退款数量
 * @property int|null $is_after_sale 售后状态
 * @property string|null $promoter_code 推广码
 * @property int|null $promoter_id 推广人ID
 * @property string|null $promoter_nickname 推广人昵称
 * @property int|null $status 状态[-1:删除;0:禁用;1启用]
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class Order extends \common\models\base\BaseModel
{
    use HasOneMerchant;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_order}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'receiver_id',
                    'receiver_realname',
                    'receiver_name',
                    'receiver_mobile',
                    'receiver_details',
                    'receiver_province_id',
                    'receiver_city_id',
                    'receiver_area_id'
                ],
                'required',
                'on' => 'address'
            ],
            [
                [
                    'merchant_id',
                    'order_type',
                    'pay_type',
                    'shipping_type',
                    'buyer_id',
                    'receiver_province_id',
                    'receiver_city_id',
                    'receiver_area_id',
                    'seller_star',
                    'consign_time_adjust',
                    'product_type',
                    'product_count',
                    'point',
                    'marketing_id',
                    'marketing_product_id',
                    'wholesale_record_id',
                    'give_point',
                    'give_growth',
                    'order_status',
                    'pay_status',
                    'shipping_status',
                    'feedback_status',
                    'is_evaluate',
                    'store_id',
                    'invoice_id',
                    'express_company_id',
                    'give_point_type',
                    'give_growth_type',
                    'caballero_member_id',
                    'pay_time',
                    'receiving_time',
                    'consign_time',
                    'sign_time',
                    'finish_time',
                    'close_time',
                    'auto_sign_time',
                    'auto_finish_time',
                    'auto_evaluate_time',
                    'subscribe_shipping_start_time',
                    'subscribe_shipping_end_time',
                    'is_new_member',
                    'is_print',
                    'is_oversold',
                    'refund_num',
                    'is_after_sale',
                    'promoter_id',
                    'status',
                    'created_at',
                    'updated_at'
                ],
                'integer'
            ],
            [
                [
                    'shipping_money',
                    'product_money',
                    'product_original_money',
                    'product_profit_price',
                    'order_money',
                    'pay_money',
                    'final_money',
                    'marketing_money',
                    'give_coin',
                    'tax_money',
                    'refund_money'
                ],
                'number'
            ],
            [['merchant_title', 'receiver_longitude', 'receiver_latitude', 'promoter_nickname'], 'string', 'max' => 100],
            [
                [
                    'order_sn',
                    'unite_no',
                    'order_from',
                    'out_trade_no',
                    'buyer_nickname',
                    'receiver_realname',
                    'marketing_type',
                    'fixed_telephone',
                    'distribution_time_out',
                    'promoter_code'
                ],
                'string',
                'max' => 50
            ],
            [['buyer_ip', 'receiver_zip'], 'string', 'max' => 20],
            [['buyer_message', 'receiver_name', 'receiver_details'], 'string', 'max' => 200],
            [['receiver_mobile'], 'string', 'max' => 11],
            [['seller_memo'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '订单id',
            'merchant_id' => '商户id',
            'merchant_title' => '商户店铺名称',
            'order_sn' => '订单编号',
            'unite_no' => '订单关联编号(多订单)',
            'order_from' => '订单来源',
            'out_trade_no' => '外部交易号',
            'order_type' => '订单类型',
            'pay_type' => '支付类型',
            'shipping_type' => '订单配送方式',
            'buyer_id' => '买家id',
            'buyer_nickname' => '买家会员名称',
            'buyer_ip' => '买家ip',
            'buyer_message' => '买家附言',
            'receiver_id' => '收货地址ID',
            'receiver_mobile' => '收货人手机号码',
            'receiver_province_id' => '收货人所在省',
            'receiver_city_id' => '收货人所在城市',
            'receiver_area_id' => '收货人所在街道',
            'receiver_name' => '收货人地址',
            'receiver_details' => '收货人详细地址',
            'receiver_zip' => '收货人邮编',
            'receiver_realname' => '收货人姓名',
            'receiver_longitude' => '收货人经度',
            'receiver_latitude' => '收货人纬度',
            'seller_star' => '卖家对订单的标注星标',
            'seller_memo' => '卖家对订单的备注',
            'consign_time_adjust' => '卖家延迟发货时间',
            'shipping_money' => '订单运费',
            'product_money' => '商品优惠后总价',
            'product_original_money' => '商品原本总价',
            'product_profit_price' => '商品利润',
            'product_type' => '商品类型',
            'product_count' => '订单数量',
            'order_money' => '订单总价',
            'pay_money' => '订单实付金额',
            'final_money' => '预售尾款',
            'point' => '订单消耗积分',
            'marketing_id' => '营销活动id',
            'marketing_product_id' => '营销活动产品id',
            'marketing_type' => '营销活动类型',
            'marketing_money' => '订单优惠活动金额',
            'wholesale_record_id' => '拼团记录ID',
            'give_point' => '订单赠送积分',
            'give_growth' => '赠送成长值',
            'give_coin' => '订单成功之后返购物币',
            'order_status' => '订单状态',
            'pay_status' => '订单付款状态',
            'shipping_status' => '订单配送状态',
            'feedback_status' => '订单维权状态',
            'is_evaluate' => '是否评价 0为未评价 1为已评价 2为已追评',
            'tax_money' => '税费',
            'store_id' => '门店id',
            'invoice_id' => '发票id',
            'express_company_id' => '物流公司',
            'give_point_type' => '积分返还类型 1 订单完成  2 订单收货 3  支付订单',
            'give_growth_type' => '成长值返还类型 1 订单完成  2 订单收货 3  支付订单',
            'caballero_member_id' => '骑手用户id',
            'pay_time' => '订单付款时间',
            'receiving_time' => '骑手接单时间',
            'consign_time' => '卖家发货时间',
            'sign_time' => '买家签收时间',
            'finish_time' => '订单完成时间',
            'close_time' => '关闭的时间',
            'auto_sign_time' => '自动签收时间',
            'auto_finish_time' => '自动完成时间',
            'auto_evaluate_time' => '自动评价时间',
            'fixed_telephone' => '固定电话',
            'distribution_time_out' => '配送时间段',
            'subscribe_shipping_start_time' => '预约配送开始时间',
            'subscribe_shipping_end_time' => '预约配送结束时间',
            'is_new_member' => '是否新顾客',
            'is_print' => '已打印 0未打印1已打印',
            'is_oversold' => '是否超卖',
            'refund_money' => '退款金额',
            'refund_num' => '退款数量',
            'is_after_sale' => '售后状态',
            'promoter_code' => '推广码',
            'promoter_id' => '推广人ID',
            'promoter_nickname' => '推广人昵称',
            'status' => '状态[-1:删除;0:禁用;1启用]',
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
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'id']);
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
    public function getExpressCompany()
    {
        return $this->hasOne(ExpressCompany::class, ['id' => 'express_company_id']);
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
     * 用户信息
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(Provinces::class, ['id' => 'receiver_city']);
    }

    /**
     * 订单商品
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
        return $this->hasMany(MarketingDetail::class, ['order_id' => 'id']);
    }

    /**
     * 订单虚拟商品
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductVirtual()
    {
        return $this->hasMany(ProductVirtual::class, ['order_sn' => 'order_sn']);
    }

    /**
     * 订单商品物流配送
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductExpress()
    {
        return $this->hasMany(ProductExpress::class, ['order_id' => 'id']);
    }

    /**
     * 拼团记录
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWholesaleRecord()
    {
        return $this->hasOne(WholesaleRecord::class, ['id' => 'wholesale_record_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayLog()
    {
        return $this->hasMany(PayLog::class, ['order_sn' => 'order_sn'])
            ->andWhere(['pay_status' => StatusEnum::ENABLED, 'addon_name' => 'TinyShop']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStore()
    {
        return $this->hasOne(Store::class, ['order_id' => 'id']);
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        // 未支付/未付款
        if ($this->isNewRecord || $this->order_status == OrderStatusEnum::NOT_PAY) {
            // 支付金额为商品金额
            $this->pay_money = $this->product_money;

            // 发票(税收)
            if (!empty($this->invoice_id)) {
                $config = Yii::$app->tinyShopService->config->setting();
                $order_invoice_tax = $config['order_invoice_tax'] ?? 0;
                $order_invoice_tax = $order_invoice_tax > 0 ? BcHelper::div($order_invoice_tax, 100, 4) : 0;
                $this->tax_money = BcHelper::mul($this->pay_money, $order_invoice_tax);
                $this->pay_money += $this->tax_money;
            }

            // 实付增加运费
            $this->pay_money += $this->shipping_money;
            // 订单总金额
            $this->order_money = $this->product_money + $this->shipping_money;
            !$this->isNewRecord && Invoice::updateAll(['tax_money' => $this->tax_money], ['order_id' => $this->id]);
        }

        // 修改订单商品状态
        if (!$this->isNewRecord && $this->order_status != $this->oldAttributes['order_status']) {
            OrderProduct::updateAll(
                ['order_status' => $this->order_status],
                [
                    'order_status' => $this->oldAttributes['order_status'],
                    'order_id' => $this->id,
                ]
            );

            // 骑手端订单配送状态同步
            $this->caballero_member_id !== 0 && Yii::$app->tinyErrandService->order->updateStatusByMapId($this->id, $this->order_status);
        }

        return parent::beforeSave($insert);
    }
}
