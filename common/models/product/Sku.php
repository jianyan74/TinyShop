<?php
namespace addons\TinyShop\common\models\product;

use common\models\base\BaseModel;

/**
 * This is the model class for table "{{%addon_shop_product_sku}}".
 *
 * @property string $id
 * @property int $product_id 商品编码
 * @property string $name sku名称
 * @property string $picture 主图
 * @property string $price 价格
 * @property int $stock 库存
 * @property string $code 商品编码
 * @property string $barcode 商品条形码
 * @property string $data sku串
 */
class Sku extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_product_sku}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'stock', 'sort', 'status', 'created_at', 'updated_at'], 'integer'],
            [['price', 'cost_price', 'market_price', 'wholesale_price'], 'number'],
            [['name'], 'string', 'max' => 600],
            [['code', 'barcode'], 'string', 'max' => 100],
            [['picture'], 'string', 'max' => 200],
            [['data'], 'string', 'max' => 300],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'name' => '名称',
            'picture' => '主图',
            'price' => '销售价',
            'cost_price' => '成本价',
            'market_price' => '市场价',
            'wholesale_price' => '拼团价',
            'stock' => '库存',
            'sort' => '排序',
            'code' => '商品编码',
            'barcode' => '商品条形码',
            'data' => 'sku串',
            'status' => '状态',
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
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    /**
     * 关联产品
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBaseProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id'])->select(['id', 'name', 'picture']);
    }
}
