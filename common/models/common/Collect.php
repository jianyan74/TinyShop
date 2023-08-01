<?php

namespace addons\TinyShop\common\models\common;

use yii\db\ActiveQuery;
use common\traits\HasOneMerchant;
use addons\TinyShop\common\models\product\Product;

/**
 * Class Collect
 * @package addons\TinyShop\common\models\common
 * @author jianyan74 <751393839@qq.com>
 */
class Collect extends Follow
{
    use HasOneMerchant;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_common_collect}}';
    }

    /**
     * @return ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'topic_id'])
            ->select([
                'id',
                'name',
                'picture',
                'star',
                'transmit_num',
                'comment_num',
                'collect_num',
                'view',
                'is_sales_visible',
                'is_stock_visible',
                'status',
            ])
            ->with('minPriceSku');
    }
}
