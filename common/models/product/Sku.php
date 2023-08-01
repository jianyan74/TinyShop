<?php

namespace addons\TinyShop\common\models\product;

use addons\TinyShop\common\models\repertory\Stock;
use Yii;
use addons\TinyShop\common\models\repertory\OrderDetail;

/**
 * This is the model class for table "{{%addon_tiny_shop_product_sku}}".
 *
 * @property int $id
 * @property int|null $merchant_id 商户id
 * @property int|null $product_id 商品编码
 * @property string|null $name sku名称
 * @property string|null $picture 商品主图
 * @property float $price 价格
 * @property float $market_price 市场价格
 * @property float $cost_price 成本价
 * @property int $stock 库存
 * @property string|null $sku_no 商品编码
 * @property string|null $barcode 商品条码
 * @property float|null $weight 商品重量
 * @property float|null $volume 商品体积
 * @property int|null $sort 排序
 * @property string|null $data sku串
 * @property int|null $status 状态[-1:删除;0:禁用;1启用]
 * @property int|null $created_at 创建时间
 * @property int|null $updated_at 更新时间
 */
class Sku extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_product_sku}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'product_id', 'stock', 'sort', 'status', 'created_at', 'updated_at'], 'integer', 'min' => 0],
            [['price', 'market_price', 'cost_price', 'weight', 'volume'], 'number', 'min' => 0, 'max' => 9999999],
            [['price', 'market_price', 'cost_price', 'weight', 'volume', 'stock'], 'required'],
            [['name', 'picture'], 'string', 'max' => 255],
            [['sku_no', 'barcode'], 'string', 'max' => 100],
            [['data'], 'string', 'max' => 500],
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
            'product_id' => '商品编码',
            'name' => 'sku名称',
            'picture' => '商品主图',
            'price' => '销售价',
            'market_price' => '划线价',
            'cost_price' => '成本价',
            'stock' => '库存',
            'sku_no' => '商品编码',
            'barcode' => '商品条码',
            'weight' => '商品重量',
            'volume' => '商品体积',
            'sort' => '排序',
            'data' => 'sku串',
            'status' => '状态[-1:删除;0:禁用;1启用]',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * 关联商品
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    /**
     * 关联仓库规格
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRepertoryStock()
    {
        return $this->hasOne(Stock::class, ['sku_id' => 'id']);
    }
}
