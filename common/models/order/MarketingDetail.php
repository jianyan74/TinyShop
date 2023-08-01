<?php

namespace addons\TinyShop\common\models\order;

use Yii;

/**
 * This is the model class for table "{{%addon_tiny_shop_order_marketing}}".
 *
 * @property int $id
 * @property int|null $order_id 订单ID
 * @property int|null $product_id 商品id
 * @property int|null $sku_id 商品skuid
 * @property int|null $marketing_id 优惠ID
 * @property string|null $marketing_type 优惠类型
 * @property string|null $marketing_name 优惠类型名称
 * @property string|null $marketing_condition 优惠说明
 * @property int|null $free_shipping 是否包邮 1包邮
 * @property int|null $discount_type 优惠金额类型 1满减;2:折扣
 * @property float|null $discount_money 优惠的金额，单位：元，精确到小数点后两位
 * @property int|null $give_point 赠送积分
 * @property int|null $give_growth 赠送成长值
 * @property array|null $give_coupon_type 赠送的优惠券id
 * @property array|null $gift 赠品
 * @property string|null $uuid
 * @property string|null $remark 备注
 * @property int|null $created_at 创建时间
 * @property int|null $updated_at 修改时间
 */
class MarketingDetail extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_order_marketing_detail}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'product_id', 'sku_id', 'marketing_id', 'free_shipping', 'discount_type', 'give_point', 'give_growth', 'created_at', 'updated_at'], 'integer'],
            [['discount_money'], 'number'],
            [['uuid', 'give_coupon_type', 'gift'], 'safe'],
            [['marketing_type', 'marketing_name', 'remark'], 'string', 'max' => 100],
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
            'product_id' => '商品id',
            'sku_id' => '商品skuid',
            'marketing_id' => '优惠ID',
            'marketing_type' => '优惠类型',
            'marketing_name' => '优惠类型名称',
            'marketing_condition' => '优惠说明',
            'free_shipping' => '是否包邮 1包邮',
            'discount_type' => '优惠金额类型 1满减;2:折扣',
            'discount_money' => '优惠的金额，单位：元，精确到小数点后两位',
            'give_point' => '赠送积分',
            'give_growth' => '赠送成长值',
            'give_coupon_type' => '赠送的优惠券',
            'gift' => '赠品',
            'uuid' => 'Uuid',
            'remark' => '备注',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }
}
