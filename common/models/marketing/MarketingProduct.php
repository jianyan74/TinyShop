<?php

namespace addons\TinyShop\common\models\marketing;

use addons\TinyShop\common\traits\HasOneProduct;

/**
 * This is the model class for table "{{%addon_tiny_shop_marketing_product}}".
 *
 * @property int $id 主键
 * @property int|null $merchant_id 店铺ID
 * @property int|null $product_id 商品ID
 * @property int|null $marketing_id 对应活动
 * @property string|null $marketing_type 活动类型
 * @property string|null $marketing_data 活动数据
 * @property int|null $marketing_sales 销量
 * @property int|null $marketing_stock 锁定可用库存
 * @property int|null $marketing_total_stock 锁定可用总库存
 * @property float|null $discount 活动金额
 * @property int|null $discount_type 活动金额类型
 * @property int|null $decimal_reservation_number 价格保留方式 0去掉角和分 1去掉分
 * @property int|null $number 参与数量
 * @property int|null $min_buy 最少购买
 * @property int|null $max_buy 每人限购 0无限制
 * @property int|null $prediction_time 预告时间
 * @property int|null $start_time 开始时间
 * @property int|null $end_time 结束时间
 * @property int|null $status 状态
 * @property int|null $is_template 模板
 */
class MarketingProduct extends \yii\db\ActiveRecord
{
    use HasOneProduct;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_marketing_product}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['number', 'marketing_id', 'marketing_type', 'discount'], 'required'],
            [
                [
                    'merchant_id',
                    'product_id',
                    'marketing_id',
                    'discount_type',
                    'marketing_sales',
                    'marketing_stock',
                    'marketing_total_stock',
                    'prediction_time',
                    'start_time',
                    'end_time',
                    'number',
                    'decimal_reservation_number',
                    'min_buy',
                    'max_buy',
                    'status',
                    'is_template'
                ],
                'integer'
            ],
            [['marketing_data'], 'safe'],
            [['discount'], 'number', 'min' => 0],
            [['number'], 'integer', 'min' => 1],
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
            'marketing_id' => '对应活动',
            'marketing_type' => '活动类型',
            'marketing_data' => '活动数据',
            'marketing_sales' => '销量',
            'marketing_stock' => '锁定可用库存',
            'marketing_total_stock' => '锁定可用总库存',
            'discount' => '活动金额',
            'discount_type' => '活动金额类型',
            'decimal_reservation_number' => '价格保留方式 0去掉角和分 1去掉分',
            'number' => '参与数量',
            'min_buy' => '最少购买',
            'max_buy' => '每人限购 0无限制',
            'prediction_time' => '预告时间',
            'start_time' => '开始时间',
            'end_time' => '结束时间',
            'status' => '状态',
            'is_template' => '模板', // 如果是秒杀/限时折扣存在多个时间段就会是模板
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSku()
    {
        return $this->hasMany(MarketingProductSku::class, ['marketing_product_id' => 'id']);
    }

    /**
     * 最低价
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMinSku()
    {
        return $this->hasOne(MarketingProductSku::class, ['marketing_product_id' => 'id'])->orderBy('marketing_price asc');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGift()
    {
        return $this->hasOne(Gift::class, ['id' => 'marketing_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCombination()
    {
        return $this->hasOne(Combination::class, ['id' => 'marketing_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFullGive()
    {
        return $this->hasOne(FullGive::class, ['id' => 'marketing_id']);
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        empty($this->min_buy) && $this->min_buy = 1;
        empty($this->max_buy) && $this->max_buy = 0;
        empty($this->marketing_data) && $this->marketing_data = [];
        empty($this->decimal_reservation_number) && $this->decimal_reservation_number = 0;

        return parent::beforeSave($insert);
    }
}
