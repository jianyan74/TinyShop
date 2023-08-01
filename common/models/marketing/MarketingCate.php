<?php

namespace addons\TinyShop\common\models\marketing;

use common\behaviors\MerchantBehavior;
use Yii;

/**
 * This is the model class for table "{{%addon_tiny_shop_marketing_cate}}".
 *
 * @property int $id 主键
 * @property int|null $merchant_id 店铺ID
 * @property int|null $cate_id 分类ID
 * @property int|null $marketing_id 对应活动
 * @property string|null $marketing_type 活动类型
 * @property int|null $prediction_time 预告时间
 * @property int|null $start_time 开始时间
 * @property int|null $end_time 结束时间
 * @property int|null $status 状态
 */
class MarketingCate extends \yii\db\ActiveRecord
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_marketing_cate}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'cate_id', 'marketing_id', 'prediction_time', 'start_time', 'end_time', 'status'], 'integer'],
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
            'cate_id' => '分类ID',
            'marketing_id' => '对应活动',
            'marketing_type' => '活动类型',
            'prediction_time' => '预告时间',
            'start_time' => '开始时间',
            'end_time' => '结束时间',
            'status' => '状态',
        ];
    }
}
