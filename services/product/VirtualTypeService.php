<?php

namespace addons\TinyShop\services\product;

use common\components\Service;
use addons\TinyShop\common\models\product\VirtualType;

/**
 * Class VirtualTypeService
 * @package addons\TinyShop\services\product
 * @author jianyan74 <751393839@qq.com>
 */
class VirtualTypeService extends Service
{
    /**
     * @param $product_id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findByProductId($product_id)
    {
        return VirtualType::find()
            ->where(['product_id' => $product_id])
            ->one();
    }

    /**
     * @param array $ids
     * @return array|bool|\yii\db\ActiveRecord[]
     */
    public function findByProductIds(array $ids)
    {
        if (!$ids) {
            return [];
        }

        return VirtualType::find()
            ->where(['in', 'product_id', $ids])
            ->asArray()
            ->all();
    }
}