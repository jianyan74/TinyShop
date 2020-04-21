<?php

namespace addons\TinyShop\common\models\product;

use common\enums\StatusEnum;
use common\models\member\Level;

/**
 * This is the model class for table "{{%addon_shop_product_member_discount}}".
 *
 * @property int $id 折扣id
 * @property int $member_level 会员级别
 * @property string $product_id 商品id
 * @property int $discount 折扣
 * @property int $decimal_reservation_number 价格保留方式 0 去掉角和分，1去掉分，2 保留角和分
 */
class MemberDiscount extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_product_member_discount}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['discount'], 'integer', 'min' => 0, 'max' => 100],
            [['member_level', 'decimal_reservation_number', 'product_id'], 'integer'],
            [['product_id'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '折扣id',
            'member_level' => '会员级别',
            'product_id' => '商品id',
            'discount' => '折扣',
            'decimal_reservation_number' => '价格保留方式 0 去掉角和分，1去掉分，2 保留角和分',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMemberLevel()
    {
        return $this->hasOne(Level::class, ['level' => 'member_level'])->where(['status' => StatusEnum::ENABLED]);
    }
}
