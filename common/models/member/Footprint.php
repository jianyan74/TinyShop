<?php

namespace addons\TinyShop\common\models\member;

use addons\TinyShop\common\traits\HasOneProduct;

/**
 * This is the model class for table "{{%addon_tiny_shop_member_footprint}}".
 *
 * @property int $id
 * @property int|null $merchant_id 商户id
 * @property int|null $product_id 商品id
 * @property int|null $member_id 用户id
 * @property int|null $cate_id 商品分类
 * @property int|null $num 浏览次数
 * @property int|null $status 状态
 * @property int|null $created_at 创建时间
 * @property int|null $updated_at 更新时间
 */
class Footprint extends \common\models\base\BaseModel
{
    use HasOneProduct;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_member_footprint}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'product_id', 'member_id', 'cate_id', 'num', 'status', 'created_at', 'updated_at'], 'integer'],
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
            'product_id' => '商品id',
            'member_id' => '用户id',
            'cate_id' => '商品分类',
            'num' => '浏览次数',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
