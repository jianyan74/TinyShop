<?php

namespace addons\TinyShop\common\models\order;

use Yii;

/**
 * This is the model class for table "{{%addon_shop_order_product_marketing_detail}}".
 *
 * @property int $id
 * @property int $order_id 订单ID
 * @property int $product_id 产品id
 * @property int $sku_id 产品skuid
 * @property int $marketing_id 优惠ID
 * @property string $marketing_type 优惠类型
 * @property string $marketing_name 优惠类型名称
 * @property string $marketing_condition 优惠说明
 * @property int $free_shipping 是否包邮 1包邮
 * @property int $discount_type 优惠金额类型 1满减;2:折扣
 * @property string $discount_money 优惠的金额，单位：元，精确到小数点后两位
 * @property int $give_point 赠送积分
 * @property int $give_coupon_type_id 赠送的优惠券id
 * @property int $gift_id 赠品id
 * @property int $created_at 创建时间
 * @property int $updated_at 修改时间
 */
class ProductMarketingDetail extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_order_product_marketing_detail}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'product_id', 'sku_id', 'marketing_id', 'free_shipping', 'discount_type', 'give_point', 'give_coupon_type_id', 'gift_id', 'created_at', 'updated_at'], 'integer'],
            [['discount_money'], 'number'],
            [['marketing_type', 'marketing_name'], 'string', 'max' => 100],
            [['marketing_condition'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => '订单ID',
            'product_id' => '产品id',
            'sku_id' => '产品skuid',
            'marketing_id' => '优惠ID',
            'marketing_type' => '优惠类型',
            'marketing_name' => '优惠类型名称',
            'marketing_condition' => '优惠说明',
            'free_shipping' => '是否包邮 1包邮',
            'discount_type' => '优惠金额类型 1满减;2:折扣',
            'discount_money' => '优惠的金额，单位：元，精确到小数点后两位',
            'give_point' => '赠送积分',
            'give_coupon_type_id' => '赠送的优惠券id',
            'gift_id' => '赠品id',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }
}
