<?php

namespace addons\TinyShop\common\models\order;

use Yii;
use yii\db\ActiveQuery;
use common\models\member\Member;
use common\models\base\BaseModel;
use addons\TinyShop\common\models\product\Product;

/**
 * This is the model class for table "{{%addon_tiny_shop_order_after_sale}}".
 *
 * @property int $id 主键id
 * @property int|null $merchant_id 店铺ID
 * @property int|null $type 售后类型[1:售中;2:售后]
 * @property int|null $order_id 订单id
 * @property string|null $order_sn 订单编号
 * @property int|null $order_product_id 订单项id
 * @property int|null $store_id 门店id
 * @property int|null $buyer_id 用户id
 * @property string|null $buyer_nickname 用户昵称
 * @property int|null $product_id 商品id
 * @property int|null $sku_id skuID
 * @property int|null $number 购买数量
 * @property float|null $refund_apply_money 退款申请金额
 * @property int|null $refund_type 退款方式
 * @property int|null $refund_pay_type 付款方式
 * @property string|null $refund_reason 退款原因
 * @property string|null $refund_explain 退款说明
 * @property string|null $refund_evidence 退款凭证
 * @property int|null $refund_status 退款状态
 * @property float|null $refund_money 订单退款余额
 * @property int|null $refund_time 退款时间
 * @property string|null $member_express_company 退款物流公司名称
 * @property string|null $member_express_no 退款物流单号
 * @property int|null $member_express_mobile 手机号码
 * @property int|null $member_express_time 退款物流时间
 * @property int|null $merchant_shipping_type 发货方式1 需要物流 0无需物流
 * @property int|null $merchant_express_company_id 快递公司id
 * @property string|null $merchant_express_company 物流公司名称
 * @property string|null $merchant_express_no 运单编号
 * @property int|null $merchant_express_mobile 商家手机号码
 * @property int|null $merchant_express_time 退款物流时间
 * @property string|null $memo 备注
 * @property int|null $status 状态[-1:删除;0:禁用;1启用]
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class AfterSale extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_order_after_sale}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refund_money'], 'required'],
            [
                [
                    'merchant_id',
                    'type',
                    'order_id',
                    'order_product_id',
                    'store_id',
                    'buyer_id',
                    'product_id',
                    'sku_id',
                    'number',
                    'refund_type',
                    'refund_pay_type',
                    'refund_status',
                    'refund_time',
                    'member_express_time',
                    'merchant_shipping_type',
                    'merchant_express_company_id',
                    'merchant_express_time',
                    'status',
                    'created_at',
                    'updated_at',
                ],
                'integer',
            ],
            [['refund_apply_money', 'refund_money'], 'number'],
            [['refund_evidence'], 'safe'],
            [['order_sn'], 'string', 'max' => 30],
            [['merchant_express_mobile', 'member_express_mobile'], 'string', 'max' => 20],
            [['buyer_nickname', 'member_express_company', 'member_express_no'], 'string', 'max' => 100],
            [['refund_reason', 'merchant_express_company', 'memo'], 'string', 'max' => 255],
            [['refund_explain'], 'string', 'max' => 200],
            [['merchant_express_no'], 'string', 'max' => 50],
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
            'type' => '出售状态', // 售后
            'order_id' => '订单id',
            'order_sn' => '订单编号',
            'order_product_id' => '订单项id',
            'store_id' => '门店id',
            'buyer_id' => '用户id',
            'buyer_nickname' => '用户昵称',
            'product_id' => '商品id',
            'sku_id' => 'skuID',
            'number' => '购买数量',
            'refund_apply_money' => '退款申请金额',
            'refund_type' => '退款方式',
            'refund_pay_type' => '付款方式',
            'refund_reason' => '退款原因',
            'refund_explain' => '退款说明',
            'refund_evidence' => '退款凭证',
            'refund_status' => '退款状态',
            'refund_money' => '退款金额',
            'refund_time' => '退款时间',
            'member_express_company' => '退款物流公司名称',
            'member_express_no' => '退款物流单号',
            'member_express_mobile' => '退款手机号码',
            'member_express_time' => '退款物流时间',
            'merchant_shipping_type' => '发货方式', // 1 需要物流 0 无需物流
            'merchant_express_company_id' => '快递公司id',
            'merchant_express_company' => '物流公司名称',
            'merchant_express_no' => '运单编号',
            'merchant_express_mobile' => '手机号码',
            'merchant_express_time' => '退款物流时间',
            'memo' => '备注',
            'status' => '状态',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOrderProduct()
    {
        return $this->hasOne(OrderProduct::class, ['id' => 'order_product_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Member::class, ['id' => 'buyer_id']);
    }

    /**
     * @param $insert
     * @param $changedAttributes
     * @return void
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            OrderProduct::updateAll(['refund_status' => $this->refund_status, 'refund_type' => $this->refund_type, 'after_sale_id' => $this->id], ['id' => $this->order_product_id]);
        } else {
            OrderProduct::updateAll(['refund_status' => $this->refund_status], ['after_sale_id' => $this->id]);
        }

        parent::afterSave($insert, $changedAttributes);
    }
}
