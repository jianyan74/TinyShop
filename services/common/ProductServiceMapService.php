<?php

namespace addons\TinyShop\services\common;

use common\components\Service;
use common\helpers\ArrayHelper;
use common\enums\AuditStatusEnum;
use addons\TinyShop\common\models\common\ProductServiceMap;

/**
 * 商品服务
 *
 * Class ProductServiceMapService
 * @package addons\TinyShop\services\common
 * @author jianyan74 <751393839@qq.com>
 */
class ProductServiceMapService extends Service
{
    /**
     * @param $merchant_id
     */
    public function findById($id)
    {
        return ProductServiceMap::find()
            ->where(['id' => $id])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->one();
    }

    /**
     * @param $merchant_id
     */
    public function findByMerchantId($merchant_id)
    {
        $data = ProductServiceMap::find()
            ->where([
                'merchant_id' => $merchant_id,
                'audit_status' => AuditStatusEnum::ENABLED,
                'status' => AuditStatusEnum::ENABLED
            ])
            ->with(['productService'])
            ->asArray()
            ->all();

        return ArrayHelper::getColumn($data, 'productService');
    }
}
