<?php

namespace addons\TinyShop\common\models\marketing;

use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%addon_tiny_shop_marketing_stat}}".
 *
 * @property int $id 主键
 * @property int|null $merchant_id 店铺ID
 * @property int|null $marketing_id 对应活动
 * @property string|null $marketing_type 活动类型
 * @property int|null $total_customer_num 总客户数量
 * @property int|null $new_customer_num 新客户数量
 * @property int|null $old_customer_num 老客户数量
 * @property int|null $pay_money 订单实付金额
 * @property int|null $order_count 订单数量
 * @property int|null $product_count 订单产品数量
 * @property float|null $discount_money 优惠总金额
 * @property int|null $status 状态
 * @property int|null $created_at 创建时间
 * @property int|null $updated_at 修改时间
 */
class MarketingStat extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_marketing_stat}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'marketing_id', 'total_customer_num', 'new_customer_num', 'old_customer_num', 'pay_money', 'order_count', 'product_count', 'status', 'created_at', 'updated_at'], 'integer'],
            [['discount_money'], 'number'],
            [['marketing_type'], 'string', 'max' => 60],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键',
            'merchant_id' => '店铺ID',
            'marketing_id' => '对应活动',
            'marketing_type' => '活动类型',
            'total_customer_num' => '总客户数量',
            'new_customer_num' => '新客户数量',
            'old_customer_num' => '老客户数量',
            'pay_money' => '订单实付金额',
            'order_count' => '订单数量',
            'product_count' => '订单产品数量',
            'discount_money' => '优惠总金额',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }
}
