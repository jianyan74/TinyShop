<?php

namespace addons\TinyShop\common\models\order;

use yii\db\ActiveQuery;
use common\models\base\BaseModel;
use common\models\member\Member;
use addons\TinyShop\common\models\product\Product;

/**
 * This is the model class for table "{{%addon_tiny_shop_order_product}}".
 *
 * @property int $id 订单项ID
 * @property int|null $merchant_id 店铺ID
 * @property int|null $buyer_id 购买人ID
 * @property int|null $order_id 订单ID
 * @property string|null $order_sn 订单编号
 * @property int|null $store_id 门店id
 * @property int|null $product_id 商品ID
 * @property string|null $product_name 商品名称
 * @property float|null $product_money 商品优惠后总价
 * @property float|null $product_original_money 商品原本总价
 * @property string|null $product_picture 商品图片
 * @property int|null $sku_id skuID
 * @property string|null $sku_name sku名称
 * @property float|null $price 商品价格
 * @property float|null $cost_price 商品成本价
 * @property float|null $profit_price 商品利润
 * @property int|null $num 购买数量
 * @property float|null $adjust_money 调整金额
 * @property int|null $point_exchange_type 积分兑换类型0.非积分兑换1.积分兑换
 * @property int|null $product_type 虚拟商品类型
 * @property int|null $stock_deduction_type 库存扣减类型
 * @property int|null $marketing_id 促销ID
 * @property int|null $marketing_product_id 促销产品ID
 * @property string|null $marketing_type 促销类型
 * @property int|null $order_type 订单类型
 * @property int|null $order_status 订单状态
 * @property int|null $give_point 积分数量
 * @property int|null $give_growth 赠送成长值
 * @property float|null $give_coin 订单成功之后返购物币
 * @property int|null $shipping_status 物流状态
 * @property int|null $is_oversold 是否超卖
 * @property int|null $is_evaluate 是否评价 0为未评价 1为已评价 2为已追评
 * @property int|null $supplier_id 供应商
 * @property string|null $supplier_name 供货商名称
 * @property int|null $gift_flag 赠品标识，0:不是赠品，大于0：赠品id
 * @property float|null $refund_money 退款金额
 * @property int|null $refund_num 退款数量
 * @property int|null $refund_status 售后状态
 * @property int|null $refund_type 售后类型
 * @property int|null $after_sale_id 售后ID
 * @property int|null $is_commission 是否支持分销
 * @property int|null $status 状态[-1:删除;0:禁用;1启用]
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class OrderProduct extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_order_product}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'merchant_id',
                    'buyer_id',
                    'order_id',
                    'store_id',
                    'product_id',
                    'sku_id',
                    'num',
                    'point_exchange_type',
                    'product_type',
                    'stock_deduction_type',
                    'marketing_id',
                    'marketing_product_id',
                    'order_type',
                    'order_status',
                    'give_point',
                    'give_growth',
                    'shipping_status',
                    'is_oversold',
                    'is_evaluate',
                    'supplier_id',
                    'gift_flag',
                    'refund_num',
                    'refund_type',
                    'refund_status',
                    'after_sale_id',
                    'is_commission',
                    'status',
                    'created_at',
                    'updated_at',
                ],
                'integer',
            ],
            [
                [
                    'product_money',
                    'product_original_money',
                    'price',
                    'cost_price',
                    'profit_price',
                    'adjust_money',
                    'give_coin',
                    'refund_money',
                ],
                'number',
            ],
            [['order_sn', 'product_name', 'sku_name'], 'string', 'max' => 100],
            [['product_picture'], 'string', 'max' => 255],
            [['marketing_type', 'supplier_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '订单项ID',
            'merchant_id' => '店铺ID',
            'buyer_id' => '购买人ID',
            'order_id' => '订单ID',
            'order_sn' => '订单编号',
            'store_id' => '门店id',
            'product_id' => '商品ID',
            'product_name' => '商品名称',
            'product_money' => '商品优惠后总价',
            'product_original_money' => '商品原本总价',
            'product_picture' => '商品图片',
            'sku_id' => 'skuID',
            'sku_name' => 'sku名称',
            'price' => '商品价格',
            'cost_price' => '商品成本价',
            'profit_price' => '商品利润',
            'num' => '购买数量',
            'adjust_money' => '调整金额',
            'point_exchange_type' => '积分兑换类型0.非积分兑换1.积分兑换',
            'product_type' => '虚拟商品类型',
            'stock_deduction_type' => '库存扣减类型',
            'marketing_id' => '促销ID',
            'marketing_product_id' => '促销产品ID',
            'marketing_type' => '促销类型',
            'order_type' => '订单类型',
            'order_status' => '订单状态',
            'give_point' => '积分数量',
            'give_growth' => '赠送成长值',
            'give_coin' => '订单成功之后返购物币',
            'shipping_status' => '物流状态',
            'is_oversold' => '是否超卖',
            'is_evaluate' => '是否评价 0为未评价 1为已评价 2为已追评',
            'supplier_id' => '供应商',
            'supplier_name' => '供货商名称',
            'gift_flag' => '赠品标识，0:不是赠品，大于0：赠品id',
            'refund_money' => '退款金额',
            'refund_num' => '退款数量',
            'refund_type' => '售后类型',
            'refund_status' => '售后状态',
            'after_sale_id' => '售后ID',
            'is_commission' => '是否支持分销',
            'status' => '状态[-1:删除;0:禁用;1启用]',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * 用户信息
     *
     * @return ActiveQuery
     */
    public function getAfterSale()
    {
        return $this->hasOne(AfterSale::class, ['id' => 'receiver_city']);
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
    public function getMember()
    {
        return $this->hasOne(Member::class, ['id' => 'buyer_id']);
    }
}
