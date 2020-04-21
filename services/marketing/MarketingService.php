<?php

namespace addons\TinyShop\services\marketing;

use Yii;
use common\components\Service;
use addons\TinyShop\common\enums\ProductMarketingEnum;
use addons\TinyShop\common\enums\PointExchangeTypeEnum;

/**
 * Class MarketingService
 * @package addons\TinyShop\services\marketing
 * @author jianyan74 <751393839@qq.com>
 */
class MarketingService extends Service
{
    /**
     * 查询营销
     *
     * @param $product
     * @return array
     */
    public function findByIdAndType($product)
    {
        $marketing = [];
        if (PointExchangeTypeEnum::isIntegralBuy($product['point_exchange_type'])) {
            return $marketing;
        }

        return $marketing;
    }
}