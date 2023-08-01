<?php

namespace addons\TinyShop\services\marketing;

use yii\db\ActiveRecord;
use common\components\Service;
use common\enums\StatusEnum;
use addons\TinyShop\common\models\marketing\MarketingProductSku;

/**
 * Class MarketingProductSkuService
 * @package addons\TinyShop\services\marketing
 * @author jianyan74 <751393839@qq.com>
 */
class MarketingProductSkuService extends Service
{
    /**
     * @param $product_id
     * @param $sku_id
     * @param $marketing_id
     * @param $marketing_type
     * @return array|ActiveRecord[]|MarketingProductSku
     */
    public function findByIdAndMarketing(
        $product_id,
        $sku_id,
        $marketing_id,
        $marketing_type,
        $marketing_product_id = ''
    ) {
        return MarketingProductSku::find()
            ->where(['product_id' => $product_id])
            ->andWhere(['in', 'sku_id', [0, $sku_id]])
            ->andWhere([
                'marketing_id' => $marketing_id,
                'marketing_type' => $marketing_type,
                'status' => StatusEnum::ENABLED,
                'is_template' => 0,
            ])
            ->andFilterWhere(['marketing_product_id' => $marketing_product_id])
            ->all();
    }
}
