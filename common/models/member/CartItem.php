<?php

namespace addons\TinyShop\common\models\member;

use common\traits\HasOneMerchant;
use addons\TinyShop\common\models\product\Sku;
use addons\TinyShop\common\traits\HasOneProduct;

/**
 * This is the model class for table "{{%addon_tiny_shop_member_cart_item}}".
 *
 * @property int $id
 * @property int|null $merchant_id 商户id
 * @property int|null $member_id 用户编码
 * @property float|null $price 价格
 * @property int|null $number 商品数量
 * @property int|null $product_id 商品编码
 * @property string|null $product_picture 商品快照
 * @property string|null $product_name 商品名称
 * @property int|null $sku_id 商品sku编码
 * @property string|null $sku_name 商品sku信息
 * @property int|null $status 状态[-1:删除;0:禁用;1启用]
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class CartItem extends \common\models\base\BaseModel
{
    use HasOneMerchant, HasOneProduct;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_member_cart_item}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'member_id', 'marketing_id', 'number', 'product_id', 'sku_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['price'], 'number'],
            [['product_picture'], 'string', 'max' => 200],
            [['marketing_type'], 'string', 'max' => 50],
            [['product_name', 'sku_name'], 'string', 'max' => 255],
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
            'member_id' => '用户编码',
            'marketing_id' => '营销ID',
            'marketing_type' => '营销类型',
            'price' => '价格',
            'number' => '商品数量',
            'product_id' => '商品编码',
            'product_picture' => '商品快照',
            'product_name' => '商品名称',
            'sku_id' => '商品sku编码',
            'sku_name' => '商品sku信息',
            'status' => '状态[-1:删除;0:禁用;1启用]',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
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
}
