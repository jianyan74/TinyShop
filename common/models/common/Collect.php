<?php

namespace addons\TinyShop\common\models\common;

use addons\TinyShop\common\models\product\Product;
use common\traits\HasOneMerchant;

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
        return '{{%addon_shop_common_collect}}';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'topic_id'])
            ->select(['id', 'name', 'picture', 'star', 'transmit_num', 'comment_num', 'collect_num', 'view', 'product_status', 'status'])
            ->with('minPriceSku');
    }
}
