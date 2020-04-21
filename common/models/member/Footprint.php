<?php

namespace addons\TinyShop\common\models\member;

use addons\TinyShop\common\models\product\Product;
use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%addon_shop_member_footprint}}".
 *
 * @property string $id
 * @property string $merchant_id 商户id
 * @property string $product_id 产品id
 * @property int $member_id 用户id
 * @property int $cate_id 商品分类
 * @property string $num 浏览次数
 * @property int $status 状态
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class Footprint extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_member_footprint}}';
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
            'product_id' => '产品id',
            'member_id' => '用户id',
            'cate_id' => '商品分类',
            'num' => '浏览次数',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id'])
            ->select(['id', 'name', 'picture', 'star', 'transmit_num', 'comment_num', 'collect_num', 'view', 'product_status', 'status'])
            ->with('minPriceSku');
    }
}
