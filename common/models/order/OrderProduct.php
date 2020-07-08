<?php

namespace addons\TinyShop\common\models\order;

use addons\TinyShop\common\models\product\Evaluate;
use addons\TinyShop\common\models\product\Product;
use addons\TinyShop\common\models\product\Sku;
use addons\TinyShop\common\models\product\VirtualType;
use common\models\base\BaseModel;
use common\models\member\Member;
use Yii;

/**
 * This is the model class for table "{{%addon_shop_order_product}}".
 *
 * @property string $id 订单项ID
 * @property int $order_id 订单ID
 * @property int $merchant_id 店铺ID
 * @property int $product_id 商品ID
 * @property int $member_id 用户
 * @property string $product_name 商品名称
 * @property int $sku_id skuID
 * @property string $sku_name sku名称
 * @property string $price 商品价格
 * @property string $cost_price 商品成本价
 * @property int $num 购买数量
 * @property string $adjust_money 调整金额
 * @property int $product_money 商品总价
 * @property int $product_picture 商品图片
 * @property int $buyer_id 购买人ID
 * @property int $point_exchange_type 积分兑换类型0.非积分兑换1.积分兑换
 * @property string $product_virtual_group 商品类型
 * @property int $marketing_id 促销ID
 * @property int $marketing_type 促销类型
 * @property int $order_type 订单类型
 * @property int $order_status 订单状态
 * @property int $give_point 积分数量
 * @property int $shipping_status 物流状态
 * @property int $refund_type 退款方式
 * @property string $refund_require_money 退款金额
 * @property string $refund_reason 退款原因
 * @property string $refund_shipping_code 退款物流单号
 * @property string $refund_shipping_company 退款物流公司名称
 * @property string $refund_real_money 实际退款金额
 * @property int $refund_status 退款状态
 * @property string $memo 备注
 * @property int $is_evaluate 是否评价 0为未评价 1为已评价 2为已追评
 * @property int $refund_time 退款时间
 * @property string $refund_balance_money 订单退款余额
 * @property string $tmp_express_company 批量打印时添加的临时物流公司
 * @property int $tmp_express_company_id 批量打印时添加的临时物流公司id
 * @property string $tmp_express_no 批量打印时添加的临时订单编号
 * @property int $gift_flag 赠品标识，0:不是赠品，大于0：赠品id
 */
class OrderProduct extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_order_product}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['member_id', 'order_id', 'merchant_id', 'product_id', 'sku_id', 'is_virtual', 'num', 'buyer_id', 'point_exchange_type', 'marketing_id', 'marketing_type', 'order_type', 'order_status', 'give_point', 'shipping_status', 'refund_type', 'refund_status', 'is_evaluate', 'refund_time', 'tmp_express_company_id', 'gift_flag', 'status', 'created_at', 'updated_at'], 'integer'],
            [['price', 'cost_price', 'adjust_money', 'product_original_money', 'product_money', 'refund_require_money', 'refund_real_money', 'refund_balance_money'], 'number'],
            [['product_name', 'product_picture'], 'string', 'max' => 200],
            [['sku_name', 'tmp_express_no'], 'string', 'max' => 50],
            [['product_virtual_group', 'refund_reason', 'refund_shipping_code', 'refund_shipping_company', 'memo', 'tmp_express_company'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => '用户',
            'order_id' => 'Order ID',
            'merchant_id' => 'Merchant ID',
            'product_id' => 'Product ID',
            'product_name' => 'Product Name',
            'sku_id' => 'Sku ID',
            'sku_name' => 'Sku Name',
            'price' => 'Price',
            'cost_price' => 'Cost Price',
            'num' => 'Num',
            'adjust_money' => 'Adjust Money',
            'product_money' => 'Product Money',
            'product_original_money' => 'product_original_money',
            'product_picture' => '商品图片',
            'buyer_id' => 'Buyer ID',
            'point_exchange_type' => '积分变动类型',
            'product_virtual_group' => 'Product Type',
            'marketing_id' => 'Promotion ID',
            'marketing_type' => 'Promotion Type ID',
            'order_type' => '订单类型',
            'order_status' => '订单状态',
            'give_point' => '赠送积分',
            'shipping_status' => 'Shipping Status',
            'refund_type' => '退款方式',
            'refund_require_money' => '退款金额',
            'refund_reason' => '退款原因',
            'refund_explain' => '退款说明',
            'refund_evidence' => '退款凭证',
            'refund_shipping_code' => '物流单号',
            'refund_shipping_company' => '物流名称',
            'refund_real_money' => 'Refund Real Money',
            'refund_status' => 'Refund Status',
            'memo' => '备注',
            'is_evaluate' => 'Is Evaluate',
            'refund_time' => 'Refund Time',
            'refund_balance_money' => 'Refund Balance Money',
            'tmp_express_company' => 'Tmp Express Company',
            'tmp_express_company_id' => 'Tmp Express Company ID',
            'tmp_express_no' => 'Tmp Express No',
            'gift_flag' => 'Gift Flag',
            'is_virtual' => 'Is Evaluate',
            'status' => '状态',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Member::class, ['id' => 'buyer_id']);
    }

    /**
     * 关联订单
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    /**
     * 关联评价
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEvaluate()
    {
        return $this->hasOne(Evaluate::class, ['order_product_id' => 'id'])->select(['id', 'order_product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSku()
    {
        return $this->hasOne(Sku::class, ['id' => 'sku_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    public function beforeSave($insert)
    {
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
    }
}
