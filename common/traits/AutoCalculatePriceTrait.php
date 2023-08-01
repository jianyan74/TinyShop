<?php

namespace addons\TinyShop\common\traits;

use common\helpers\ArrayHelper;
use common\helpers\BcHelper;
use addons\TinyShop\common\models\order\OrderProduct;
use addons\TinyShop\common\forms\PreviewForm;

/**
 * 自动计算优惠
 *
 * Trait AutoCalculatePriceTrait
 * @package addons\TinyShop\common\traits
 */
trait AutoCalculatePriceTrait
{
    /**
     * @var int
     */
    private $marketing_money  = 0;

    /**
     * @param PreviewForm $form
     * @return PreviewForm
     */
    public function calculatePrice(PreviewForm $form)
    {
        $this->marketing_money = 0;
        // 所有数据
        $allData = [];
        // sku 所有数据
        $skuData = [];
        foreach ($form->groupOrderProducts as $product_id => $groupOrderProduct) {
            $allData[] = [
                'uuid' => $product_id,
                'original_money' => $groupOrderProduct['product_money'], // 原始金额
                'surplus_money' => $groupOrderProduct['product_money'], // 剩余金额
                'discount_money' => 0, // 已优惠金额
                'cate_id' => [], // 商品商家分类
                'platform_cate_id' => [], // 平台分类
                'merchant_id' => $groupOrderProduct['merchant_id'], // 商家ID
            ];

            /** @var OrderProduct $orderProduct */
            foreach ($groupOrderProduct['products'] as $orderProduct) {
                $skuData[] = [
                    'uuid' => $orderProduct->sku_id,
                    'product_id' => $orderProduct->id,
                    'original_money' => $orderProduct->product_money, // 原始金额
                    'surplus_money' => $orderProduct->product_money, // 剩余金额
                    'discount_money' => 0, // 已优惠金额
                    'cate_id' => [], // 商品商家分类
                    'platform_cate_id' => [], // 平台分类
                    'merchant_id' => $groupOrderProduct['merchant_id'], // 商家ID
                ];
            }
        }

        $tmpMarketingDetails = $form->marketingDetails;
        list($allData, $form->marketingDetails) = $this->filterData($allData, $form->marketingDetails);
        // 把优惠的金额分配到商品上面
        $allData = ArrayHelper::arrayKey($allData, 'uuid');
        $skuMarketingDetails = [];
        foreach ($form->groupOrderProducts as $product_id => &$groupOrderProduct) {
            if (isset($allData[$product_id]) && $allData[$product_id]['discount_money'] > 0) {
                $groupOrderProduct['product_money'] = BcHelper::sub($groupOrderProduct['product_money'], $allData[$product_id]['discount_money']);
                $skuMarketingDetails[] = [
                    'uuid' => ArrayHelper::getColumn($groupOrderProduct['products'], 'sku_id'),
                    'discount_money' => $allData[$product_id]['discount_money']
                ];
            }
        }

        // 计算总优惠
        $form->product_money = BcHelper::sub($form->product_money, $this->marketing_money);
        $form->product_money < 0 && $form->product_money = 0;
        $form->marketing_money += $this->marketing_money;

        // 继续分配到最终商品上
        list($skuData) = $this->filterData($skuData, $skuMarketingDetails);
        $skuData = ArrayHelper::arrayKey($skuData, 'uuid');
        foreach ($form->groupOrderProducts as $product_id => &$groupOrderProduct) {
            foreach ($groupOrderProduct['products'] as $orderProduct) {
                if (isset($skuData[$orderProduct->sku_id]) && $skuData[$orderProduct->sku_id]['discount_money'] > 0) {
                    $orderProduct->product_money = BcHelper::sub($orderProduct->product_money, $skuData[$orderProduct->sku_id]['discount_money']);
                    $orderProduct->price = BcHelper::div($orderProduct->product_money, $orderProduct->num);
                }
            }
        }

        // 初始化数据
        unset($allData, $skuData, $skuMarketingDetails, $tmpMarketingDetails);

        return $form;
    }

    /**
     * @param array $allData 所有数据
     * @param array $marketingDetails 优惠数组
     * @return array
     */
    protected function filterData($allData, $marketingDetails)
    {
        if (empty($marketingDetails) || empty($allData)) {
            return [$allData, $marketingDetails];
        }

        // 可用的优惠券
        $usableMarketingDetails = [];
        foreach ($marketingDetails as &$marketingDetail) {
            if (isset($marketingDetail['used']) && $marketingDetail['used'] === true) {
                continue;
            }

            if (isset($marketingDetail['discount_money']) && $marketingDetail['discount_money'] > 0) {
                $marketingDetail['used'] = true;
                !isset($marketingDetail['uuid']) && $marketingDetail['uuid'] = []; // 指定ID
                !isset($marketingDetail['cate_id']) && $marketingDetail['cate_id'] = []; // 指定分类
                !isset($marketingDetail['platform_cate_id']) && $marketingDetail['platform_cate_id'] = []; // 指定平台分类
                !isset($marketingDetail['merchant_id']) && $marketingDetail['merchant_id'] = []; // 指定商家
                $usableMarketingDetails[] = $marketingDetail;
            }
        }

        if (empty($usableMarketingDetails)) {
            return [$allData, $marketingDetails];
        }

        // 匹配优惠符合的
        foreach ($usableMarketingDetails as &$usableMarketingDetail) {
            $usableData = [];
            foreach ($allData as $key => $datum) {
                $verifyStatus = true;
                // 验证ID匹配
                if (!empty($usableMarketingDetail['uuid']) && !in_array($datum['uuid'], $usableMarketingDetail['uuid'])) {
                    $verifyStatus = false;
                }

                // 验证商家ID匹配
                if (!empty($usableMarketingDetail['merchant_id']) && !in_array($datum['merchant_id'], $usableMarketingDetail['merchant_id'])) {
                    $verifyStatus = false;
                }

                // 验证分类匹配
                if (!empty($usableMarketingDetail['cate_id']) && empty(array_intersect($datum['cate_id'], $usableMarketingDetail['cate_id']))) {
                    $verifyStatus = false;
                }

                // 验证平台分类匹配
                if (!empty($usableMarketingDetail['platform_cate_id']) && empty(array_intersect($datum['platform_cate_id'], $usableMarketingDetail['platform_cate_id']))) {
                    $verifyStatus = false;
                }

                $verifyStatus == true && $usableData[$key] = $datum;
            }

            // 开始计算优惠
            if (!empty($usableData)) {
                $usableData = $this->allocationPrice($usableMarketingDetail['discount_money'], $usableData);
                // 重新赋值
                foreach ($usableData as $k => $usableDatum) {
                    $allData[$k] = $usableDatum;
                }
            }
        }

        return [$allData, $marketingDetails];
    }

    /**
     * 分配
     *
     * @param float $discountMoney 优惠总金额
     * @param array $usableData 待计算的数据
     */
    protected function allocationPrice($discountMoney, &$usableData)
    {
        $count = count($usableData);
        $totalMoney = 0;
        foreach ($usableData as $usableDatum) {
            $totalMoney = BcHelper::add($totalMoney, $usableDatum['surplus_money']);
        }

        // 超出优惠
        if ($discountMoney >= $totalMoney) {
            foreach ($usableData as &$usableDatum) {
                $usableDatum['discount_money'] = $usableDatum['surplus_money'];
                $usableDatum['surplus_money'] = 0;
            }

            // 记录营销金额
            $this->marketing_money = $totalMoney;

            return $usableData;
        }

        // ----------- 开始计算优惠 优惠百分比 ------------ //
        // 百分比率
        $percentage = BcHelper::div($discountMoney, $totalMoney, 20);
        foreach ($usableData as &$usableDatum) {
            if ($usableDatum['surplus_money'] <= 0 || $discountMoney <= 0) {
                continue;
            }
            // 优惠金额
            $tmpDiscount = BcHelper::mul($usableDatum['surplus_money'], $percentage);
            // 剩余优惠金额
            $discountMoney = BcHelper::sub($discountMoney, $tmpDiscount);
            // 剩余余额
            $usableDatum['surplus_money'] = BcHelper::sub($usableDatum['surplus_money'], $tmpDiscount);
            // 已优惠金额
            $usableDatum['discount_money'] = BcHelper::add($usableDatum['discount_money'], $tmpDiscount);
            // 记录营销金额
            $this->marketing_money = BcHelper::add($this->marketing_money, $tmpDiscount);
        }


        // 判断是否还有剩余
        if ($discountMoney > 0) {
            foreach ($usableData as &$usableDatum) {
                if ($usableDatum['surplus_money'] <= 0 || $discountMoney <= 0) {
                    continue;
                }

                // 剩余金额大于优惠金额
                if ($usableDatum['surplus_money'] >= $discountMoney) {
                    // 剩余余额
                    $usableDatum['surplus_money'] = BcHelper::sub($usableDatum['surplus_money'], $discountMoney);
                    // 已优惠金额
                    $usableDatum['discount_money'] = BcHelper::add($usableDatum['discount_money'], $discountMoney);
                    // 记录营销金额
                    $this->marketing_money = BcHelper::add($this->marketing_money, $discountMoney);
                    // 剩余优惠金额
                    $discountMoney = 0;
                } else {
                    // 剩余优惠金额
                    $discountMoney = BcHelper::sub($discountMoney, $usableDatum['surplus_money']);;
                    // 已优惠金额
                    $usableDatum['discount_money'] = BcHelper::add($usableDatum['discount_money'], $usableDatum['surplus_money']);
                    // 记录营销金额
                    $this->marketing_money = BcHelper::add($this->marketing_money, $usableDatum['surplus_money']);
                    // 剩余余额
                    $usableDatum['surplus_money'] = 0;
                }
            }
        }

        return $usableData;
    }
}
