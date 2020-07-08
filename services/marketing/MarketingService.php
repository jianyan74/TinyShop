<?php

namespace addons\TinyShop\services\marketing;

use common\components\Service;
use common\helpers\BcHelper;
use addons\TinyShop\common\enums\ProductMarketingEnum;

/**
 * Class MarketingService
 * @package addons\TinyShop\services\marketing
 * @author jianyan74 <751393839@qq.com>
 */
class MarketingService extends Service
{
    /**
     * 合并相同的营销显示
     *
     * @param array $data
     * @return array
     */
    public function mergeIdenticalMarketing($data = [])
    {
        if (empty($data)) {
            return [];
        }

        $marketing = [];
        foreach ($data as $datum) {
            $marketing_type = $datum['marketing_type'];
            if (
                !isset($marketing[$marketing_type]) &&
                isset($datum['discount_money']) &&
                $datum['discount_money'] > 0
            ) {
                $marketing[$marketing_type] = [
                    'discount_money' => 0,
                    'marketing_name' => $datum['marketing_name'],
                    'marketing_type' => $datum['marketing_type'],
                ];
            }

            if (
                isset($marketing[$marketing_type]) &&
                isset($datum['discount_money']) &&
                $datum['discount_money'] > 0
            ) {
                $marketing[$marketing_type]['discount_money'] = BcHelper::add($marketing[$marketing_type]['discount_money'], $datum['discount_money']);
            }

            // 单独计算积分
            if (
                !isset($marketing[ProductMarketingEnum::GIVE_POINT]) &&
                isset($datum['give_point']) &&
                $datum['give_point'] > 0
            ) {
                $marketing[ProductMarketingEnum::GIVE_POINT] = [
                    'discount_money' => 0,
                    'marketing_name' => ProductMarketingEnum::getValue(ProductMarketingEnum::GIVE_POINT),
                    'marketing_type' => ProductMarketingEnum::GIVE_POINT,
                ];
            }

            if (
                isset($marketing[ProductMarketingEnum::GIVE_POINT]) &&
                isset($datum['give_point']) &&
                $datum['give_point'] > 0
            ) {
                $marketing[ProductMarketingEnum::GIVE_POINT]['discount_money'] = BcHelper::add($marketing[ProductMarketingEnum::GIVE_POINT]['discount_money'], $datum['give_point']);
            }

            // 其他显示
            if (in_array($marketing_type, [ProductMarketingEnum::FULL_MAIL])) {
                $marketing[$marketing_type] = [
                    'discount_money' => 0,
                    'marketing_name' => $datum['marketing_condition'],
                    'marketing_type' => $datum['marketing_type'],
                ];
            }
        }

        $return = [];
        foreach ($marketing as $value) {
            $return[] = $value;
        }

        return $return;
    }
}