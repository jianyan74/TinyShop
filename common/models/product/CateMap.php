<?php

namespace addons\TinyShop\common\models\product;

use Yii;

/**
 * This is the model class for table "{{%addon_tiny_shop_product_cate_map}}".
 *
 * @property int|null $cate_id 分类id
 * @property int|null $product_id 商品id
 * @property int|null $merchant_id 商户id
 */
class CateMap extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_product_cate_map}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cate_id', 'product_id', 'merchant_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cate_id' => '分类id',
            'product_id' => '商品id',
            'merchant_id' => '商户id',
        ];
    }

    /**
     * 关联分类
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCate()
    {
        return $this->hasOne(Cate::class, ['id' => 'cate_id'])->select(['id', 'title']);
    }
}
