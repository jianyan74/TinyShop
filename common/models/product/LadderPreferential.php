<?php

namespace addons\TinyShop\common\models\product;

use addons\TinyShop\common\enums\PreferentialTypeEnum;

/**
 * This is the model class for table "{{%addon_shop_product_ladder_preferential}}".
 *
 * @property int $id 主键
 * @property int $product_id 商品关联id
 * @property int $quantity 数量
 * @property string $price 优惠价格
 */
class LadderPreferential extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_product_ladder_preferential}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'quantity', 'price'], 'required'],
            [['type'], 'in', 'range' => PreferentialTypeEnum::getKeys()],
            [['product_id', 'quantity', 'type'], 'integer', 'min' => 0],
            [['price'], 'number', 'min' => 0],
            [['type'], 'verifyType'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键',
            'product_id' => '商品关联id',
            'type' => '优惠类型',
            'quantity' => '数量',
            'price' => '优惠价格/折扣',
        ];
    }

    /**
     * @param $attribute
     */
    public function verifyType($attribute)
    {
        if ($this->type == PreferentialTypeEnum::DISCOUNT && $this->price > 100) {
            $this->addError($attribute, '折扣最大值为100');
        }
    }
}
