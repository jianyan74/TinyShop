<?php

namespace addons\TinyShop\common\models\marketing;

use Yii;

/**
 * This is the model class for table "{{%addon_tiny_shop_marketing_coupon_type_map}}".
 *
 * @property int $id
 * @property int $coupon_type_id 优惠券类型id
 * @property int|null $marketing_id 对应活动
 * @property string|null $marketing_type 活动类型
 * @property int|null $number 数量
 */
class CouponTypeMap extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_marketing_coupon_type_map}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['coupon_type_id'], 'required'],
            [['coupon_type_id', 'marketing_id', 'number'], 'integer'],
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
            'id' => 'ID',
            'coupon_type_id' => '优惠券类型id',
            'marketing_id' => '对应活动',
            'marketing_type' => '活动类型',
            'number' => '数量',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCouponType()
    {
        return $this->hasOne(CouponType::class, ['id' => 'coupon_type_id']);
    }
}
