<?php

namespace addons\TinyShop\common\components\platform;

use addons\TinyShop\common\enums\MarketingEnum;
use addons\TinyShop\common\traits\AutoCalculatePriceTrait;

/**
 * 积分抵现
 *
 * Class PlatformUsePointHandler
 * @package addons\TinyShop\common\components\platform
 * @author jianyan74 <751393839@qq.com>
 */
class PlatformUsePointHandler
{
    use AutoCalculatePriceTrait;

    /**
     * @param $discountMoney
     * @param $groupOrderProducts
     * @return array
     */
    public function execute($discountMoney, $groupOrderProducts)
    {
        // 所有数据
        $allData = [];
        foreach ($groupOrderProducts as $product_id => $groupOrderProduct) {
            $allData[] = [
                'uuid' => $product_id,
                'original_money' => $groupOrderProduct['product_money'], // 原始金额
                'surplus_money' => $groupOrderProduct['product_money'], // 剩余金额
                'discount_money' => 0, // 已优惠金额
                'cate_id' => [], // 商品商家分类
                'platform_cate_id' => [], // 平台分类
                'merchant_id' => $groupOrderProduct['merchant_id'], // 商家ID
            ];
        }

        list($allData, $marketingDetails) = $this->filterData($allData, [
            [
                'uuid' => array_keys($groupOrderProducts),
                'marketing_id' => 0,
                'marketing_type' => MarketingEnum::USE_POINT,
                'marketing_condition' => '积分抵扣:' . $discountMoney . '元',
                'discount_money' => $discountMoney,
            ]
        ]);

        $platformMarketingDetails = [];
        foreach ($allData as $allDatum) {
            if ($allDatum['discount_money'] <= 0) {
                continue;
            }

            if (!isset($platformMarketingDetails[$allDatum['merchant_id']])) {
                $platformMarketingDetails[$allDatum['merchant_id']] = [];
            }

            $platformMarketingDetails[$allDatum['merchant_id']][] = [
                'uuid' => [$allDatum['uuid']],
                'product_id' => $allDatum['uuid'],
                'marketing_id' => 0,
                'marketing_name' => '积分抵扣',
                'marketing_type' => MarketingEnum::USE_POINT,
                'marketing_condition' => '积分抵扣: ' . $allDatum['discount_money']. '元',
                'discount_money' => $allDatum['discount_money'],
            ];
        }

        return $platformMarketingDetails;
    }
}
