<?php

namespace addons\TinyShop\common\traits;

use addons\TinyShop\common\models\product\Product;

/**
 * Trait HasOneProduct
 * @package addons\TinyShop\common\traits
 */
trait HasOneProduct
{
    /**
     * 关联产品
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id'])->select([
            'id',
            'name',
            'sketch',
            'keywords',
            'picture',
            'view',
            'match_point',
            'price',
            'market_price',
            'cost_price',
            'wholesale_price',
            'stock',
            'total_sales',
            'shipping_type',
            'unit',
        ]);
    }
}