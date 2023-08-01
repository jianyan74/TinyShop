<?php

namespace addons\TinyShop\common\models\marketing;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use addons\TinyShop\common\traits\HasOneProduct;

/**
 * This is the model class for table "{{%addon_tiny_shop_marketing_product_sku}}".
 *
 * @property int $id 主键
 * @property int|null $merchant_id 店铺ID
 * @property int|null $product_id 商品ID
 * @property int|null $sku_id SKU
 * @property int|null $marketing_product_id 关联ID
 * @property int|null $marketing_id 对应活动
 * @property string|null $marketing_type 活动类型
 * @property string|null $marketing_data 活动数据
 * @property float|null $marketing_price 活动金额(临时)
 * @property float|null $discount 活动金额
 * @property int|null $discount_type 活动金额类型
 * @property int|null $marketing_sales 销量
 * @property int|null $marketing_stock 锁定可用库存
 * @property int|null $prediction_time 预告时间
 * @property int|null $start_time 开始时间
 * @property int|null $end_time 结束时间
 * @property int|null $number 参与数量
 * @property int|null $min_buy 最少购买
 * @property int|null $max_buy 每人限购 0无限制
 * @property int|null $decimal_reservation_number 价格保留方式 0去掉角和分 1去掉分
 * @property int|null $is_min_price 是否最低价 0:否 1:是
 * @property int|null $not_calculate 参加计算 0:否 1:是
 * @property int|null $status 状态
 */
class MarketingProductSku extends ActiveRecord
{
    use HasOneProduct;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_marketing_product_sku}}';
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
                    'product_id',
                    'sku_id',
                    'marketing_product_id',
                    'marketing_id',
                    'discount_type',
                    'marketing_sales',
                    'marketing_stock',
                    'prediction_time',
                    'start_time',
                    'end_time',
                    'number',
                    'min_buy',
                    'max_buy',
                    'decimal_reservation_number',
                    'is_min_price',
                    'not_calculate',
                    'status',
                ],
                'integer',
            ],
            [['marketing_data'], 'safe'],
            [['discount', 'marketing_price'], 'number'],
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
            'product_id' => '商品ID',
            'sku_id' => 'SKU',
            'marketing_product_id' => '关联ID',
            'marketing_id' => '对应活动',
            'marketing_type' => '活动类型',
            'marketing_data' => '活动数据',
            'marketing_price' => '活动金额(临时)',
            'discount' => '活动金额',
            'discount_type' => '活动金额类型',
            'marketing_sales' => '销量',
            'marketing_stock' => '锁定可用库存',
            'prediction_time' => '预告时间',
            'start_time' => '开始时间',
            'end_time' => '结束时间',
            'number' => '参与数量',
            'min_buy' => '最少购买',
            'max_buy' => '每人限购 0无限制',
            'decimal_reservation_number' => '价格保留方式', // 0:去掉角和分; 1:去掉分
            'is_min_price' => '最低价', // 0:否; 1:是
            'not_calculate' => '最低价计算', // 0:否; 1:是
            'status' => '状态',
        ];
    }

    /**
     * 关联当前营销
     *
     * @return ActiveQuery
     */
    public function getMarketingProduct()
    {
        return $this->hasOne(MarketingProduct::class, ['id' => 'marketing_product_id']);
    }
}
