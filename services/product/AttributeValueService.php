<?php

namespace addons\TinyShop\services\product;

use Yii;
use common\components\Service;
use addons\TinyShop\common\models\product\AttributeValue;

/**
 * Class AttributeValueService
 * @package addons\TinyShop\services\product
 * @author jianyan74 <751393839@qq.com>
 */
class AttributeValueService extends Service
{
    /**
     * @param $product_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findByProductId($product_id)
    {
        return AttributeValue::find()
            ->where(['product_id' => $product_id])
            ->asArray()
            ->all();
    }
}