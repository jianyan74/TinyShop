<?php

namespace addons\TinyShop\common\models\base;

use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%addon_shop_base_local_distribution_config}}".
 *
 * @property string $id
 * @property string $merchant_id 商户id
 * @property string $order_money 订单金额
 * @property string $freight 运费
 * @property int $forenoon_start 上午开始时间
 * @property int $forenoon_end 上午结束时间
 * @property int $afternoon_start 下午开始时间
 * @property int $afternoon_end 下午结束时间
 * @property int $is_start 是否是起步价
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class LocalDistributionConfig extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_base_local_distribution_config}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_money', 'freight'], 'required'],
            [['merchant_id', 'forenoon_start', 'forenoon_end', 'afternoon_start', 'afternoon_end', 'is_start', 'created_at', 'updated_at'], 'integer'],
            [['order_money', 'freight'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'merchant_id' => '商户id',
            'order_money' => '起送消费金额',
            'freight' => '起送配送费用',
            'forenoon_start' => '上午开始时间',
            'forenoon_end' => '上午结束时间',
            'afternoon_start' => '下午开始时间',
            'afternoon_end' => '下午结束时间',
            'is_start' => '是否是起步价',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
