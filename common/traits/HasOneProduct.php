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
     * 关联商品
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id'])->select([
            'id',
            'merchant_id',
            'name',
            'sketch',
            'keywords',
            'picture',
            'view',
            'match_point',
            'price',
            'market_price',
            'cost_price',
            'stock',
            'total_sales',
            'shipping_type',
            'delivery_type',
            'is_member_discount',
            'member_discount_type',
            'is_sales_visible',
            'is_stock_visible',
            'is_commission',
            'unit',
            'type',
            'stock_deduction_type',
            'min_buy',
            'max_buy',
            'tags',
            'point_exchange_type',
            'point_give_type',
            'max_use_point',
            'give_point',
            'growth_give_type',
            'give_growth',
            'supplier_id',
            'audit_status',
            'status',
        ]);
    }
}
