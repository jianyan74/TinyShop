<?php

namespace addons\TinyShop\common\models\marketing;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%addon_shop_marketing_coupon_product}}".
 *
 * @property int $id
 * @property int $coupon_type_id 优惠券类型id
 * @property int $product_id 商品id
 */
class CouponProduct extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_marketing_coupon_product}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['coupon_type_id', 'product_id'], 'required'],
            [['coupon_type_id', 'product_id'], 'integer'],
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
            'product_id' => '产品id',
        ];
    }
}
