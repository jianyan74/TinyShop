<?php

namespace addons\TinyShop\common\models\member;

use addons\TinyShop\common\models\product\MemberDiscount;
use addons\TinyShop\common\models\product\Product;
use addons\TinyShop\common\models\product\Sku;
use common\behaviors\MerchantBehavior;
use common\enums\StatusEnum;

/**
 * This is the model class for table "{{%addon_shop_member_cart_item}}".
 *
 * @property string $id
 * @property int $member_id 用户编码
 * @property int $cart_id 购物车编码
 * @property string $sku_name 商品sku信息
 * @property string $product_img 商品快照
 * @property string $product_name 商品名称
 * @property string $price 价格
 * @property int $product_id 商品编码
 * @property int $supplier_id 店铺编码
 * @property int $sku_id 商品sku编码
 * @property int $number 商品数量
 * @property int $status 状态[-1:删除;0:禁用;1启用]
 * @property int $created_at
 * @property int $updated_at
 */
class CartItem extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_member_cart_item}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'sku_id',
                    'member_id',
                    'cart_id',
                    'product_id',
                    'supplier_id',
                    'number',
                    'status',
                    'created_at',
                    'updated_at'
                ],
                'integer'
            ],
            [['product_img', 'product_name', 'product_id'], 'required'],
            [['price'], 'number'],
            [['sku_name', 'product_img', 'product_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => 'Member ID',
            'cart_id' => 'Cart ID',
            'sku_name' => 'Product Desc',
            'product_img' => 'Product Img',
            'product_name' => 'Product Name',
            'price' => 'Price',
            'product_id' => 'Product ID',
            'supplier_id' => 'Supplier ID',
            'sku_id' => 'Sku ID',
            'number' => 'Number',
            'status' => 'Status',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * 关联产品
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        $product = new Product();
        $field = $product->getAttributes();
        unset($field['covers'], $field['sketch'], $field['intro'], $field['keywords'], $field['base_attribute_format']);
        unset($product);

        return $this->hasOne(Product::class, ['id' => 'product_id'])
            ->select(array_keys($field));
    }

    /**
     * 关联sku
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSku()
    {
        return $this->hasOne(Sku::class, ['id' => 'sku_id']);
    }

    /**
     * 会员折扣
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMemberDiscount()
    {
        return $this->hasOne(MemberDiscount::class, ['product_id' => 'product_id']);
    }
}
