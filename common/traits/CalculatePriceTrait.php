<?php

namespace addons\TinyShop\common\traits;

use common\helpers\ArrayHelper;
use common\helpers\BcHelper;
use addons\TinyShop\common\models\forms\PreviewForm;
use addons\TinyShop\common\models\order\OrderProduct;

/**
 * 计算金额
 *
 * Trait CalculatePriceTrait
 * @package addons\TinyShop\common\traits
 * @author jianyan74 <751393839@qq.com>
 */
trait CalculatePriceTrait
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
        $allDiscount = 0;
        $orderDiscount = 0;
        $orderProductDiscount = [];
        foreach ($form->marketingDetails as &$marketingDetail) {
            if (isset($marketingDetail['used'])) {
                continue;
            }

            if (isset($marketingDetail['discount_money']) && $marketingDetail['discount_money'] > 0) {
                if (isset($marketingDetail['product_id'])) {
                    if (!isset($orderProductDiscount['product_id'])) {
                        $orderProductDiscount[$marketingDetail['product_id']] = 0;
                    }

                    // 某个产品的优惠
                    $orderProductDiscount[$marketingDetail['product_id']] += $marketingDetail['discount_money'];
                } else {
                    // 订单的优惠
                    $orderDiscount += $marketingDetail['discount_money'];
                }

                // 总优惠
                $allDiscount += $marketingDetail['discount_money'];
                $marketingDetail['used'] = true;
            }
        }

        // 剔除金额为0的产品
        foreach ($form->groupOrderProducts as $product_id => &$groupOrderProduct) {
            foreach ($groupOrderProduct['products'] as $orderProduct) {
                if ($orderProduct->product_money == 0) {
                    $orderProduct->price = 0;
                    $groupOrderProduct['count'] -= $orderProduct->num;
                }
            }
        }

        ($allDiscount - $orderDiscount) > 0 && $this->setProductPrice($form->groupOrderProducts, $orderProductDiscount);
        $orderDiscount > 0 && $this->allocationPrice($form->groupOrderProducts, $orderDiscount);
        $form->product_money = BcHelper::sub($form->product_money, $allDiscount);
        $form->product_money < 0 && $form->product_money = 0;
        $form->marketing_money += $this->marketing_money;

        return $form;
    }

    /**
     * 计算产品分配
     *
     * @param array $groupOrderProducts 组别产品
     * @param array $orderProductDiscount 优惠金额
     * @return mixed
     */
    public function setProductPrice($groupOrderProducts, $orderProductDiscount)
    {
        /** @var OrderProduct $orderProduct */
        foreach ($groupOrderProducts as $product_id => &$groupOrderProduct) {
            // 判断没有优惠的金额
            if (!isset($orderProductDiscount[$product_id]) || $orderProductDiscount[$product_id] == 0) {
                continue;
            }

            // 产品总优惠
            $discount = $orderProductDiscount[$product_id];
            // 产品当前金额
            $productMoney = $groupOrderProduct['product_money'];
            // 如果优惠价格过大
            if ($productMoney <= $discount) {
                // 记录营销金额
                $this->marketing_money = $productMoney;

                $orderProductDiscount[$product_id] = 0;
                $groupOrderProduct['product_money'] = 0;
                foreach ($groupOrderProduct['products'] as $orderProduct) {
                    $orderProduct->price = 0;
                    $orderProduct->product_money = 0;
                }

                continue;
            } else {
                // 记录营销金额
                $this->marketing_money = $discount;
                $groupOrderProduct['product_money'] = BcHelper::sub($productMoney, $discount);
            }

            // ----------- 开始计算优惠 优惠百分比 ------------ //

            $percentage = BcHelper::div($discount, $productMoney);
            foreach ($groupOrderProduct['products'] as $orderProduct) {
                if ($orderProduct->product_money <= 0) {
                    continue;
                }

                // 根据百分比扣减
                $discountAmount = BcHelper::mul($orderProduct->product_money, $percentage);
                if ($discountAmount > 0) {
                    $orderProduct->product_money = BcHelper::sub($orderProduct->product_money, $discountAmount);
                    $discount = BcHelper::sub($discount, $discountAmount);
                }
            }

            // 计算剩余
            foreach ($groupOrderProduct['products'] as $orderProduct) {
                if ($orderProduct->product_money <= 0) {
                    continue;
                }

                // 根据百分比扣减
                if ($groupOrderProduct['product_money'] >= $discount) {
                    $orderProduct->product_money = BcHelper::sub($orderProduct->product_money, $discount);
                    break;
                } else {
                    $orderProduct->product_money = 0;
                    $discount = BcHelper::sub($discount, $orderProduct->product_money);
                }
            }

            // 计算单价
            foreach ($groupOrderProduct['products'] as $orderProduct) {
                if ($orderProduct->product_money > 0) {
                    $orderProduct->price = BcHelper::div($orderProduct->product_money, $orderProduct->num);
                }
            }
        }

        return $groupOrderProducts;
    }

    /**
     * 分配金额
     *
     * @param $groupOrderProducts
     * @param $orderDiscount
     * @return array|bool|mixed
     */
    public function allocationPrice($groupOrderProducts, $orderDiscount)
    {
        /** @var OrderProduct $orderProduct */
        if ($orderDiscount <= 0) {
            return true;
        }

        // 如果产品金额为0 直接返回
        $productMoney = array_sum(ArrayHelper::getColumn($groupOrderProducts, 'product_money'));
        if ($productMoney <= 0) {
            return true;
        }

        // 总优惠大于等于总金额 全部设置为0
        if ($orderDiscount >= $productMoney) {
            // 记录营销金额
            $this->marketing_money = $productMoney;
            foreach ($groupOrderProducts as $product_id => $groupOrderProduct) {
                foreach ($groupOrderProduct['products'] as $orderProduct) {
                    $orderProduct->price = 0;
                    $orderProduct->product_money = 0;
                }
            }

            return true;
        }

        // ----------- 开始计算优惠 优惠百分比 ------------ //
        // 优惠百分比
        $percentage = BcHelper::div($orderDiscount, $productMoney);
        // 产品优惠
        $orderProductDiscount = [];
        // 临时价格
        $tmpData = [];
        foreach ($groupOrderProducts as $key => $groupOrderProduct) {
            $tmpData[$key] = $groupOrderProduct['product_money'];
            $orderProductDiscount[$key] = 0;
        }

        // 分配金额
        foreach ($tmpData as $product_id => $product_money) {
            // 优惠金额
            $discountAmount = BcHelper::mul($product_money, $percentage);
            if ($discountAmount > 0) {
                $tmpData[$product_id] = BcHelper::sub($product_money, $discountAmount);
                $orderProductDiscount[$product_id] = BcHelper::add($orderProductDiscount[$product_id], $discountAmount);
                $orderDiscount = BcHelper::sub($orderDiscount, $discountAmount);
            }
        }

        // 算一波剩余
        foreach ($tmpData as $product_id => $product_money) {
            if ($orderDiscount <= 0) {
                continue;
            }

            if ($product_money <= 0) {
                continue;
            }

            if ($product_money >= $orderDiscount) {
                $tmpData[$product_id] = BcHelper::sub($product_money, $orderDiscount);
                $orderProductDiscount[$product_id] = BcHelper::add($orderProductDiscount[$product_id], $orderDiscount);
                $orderDiscount = 0;
                break;
            } else {
                $tmpData[$product_id] = 0;
                $orderProductDiscount[$product_id] = BcHelper::add($orderProductDiscount[$product_id], $product_money);
                $orderDiscount = BcHelper::sub($orderDiscount, $product_money);
            }
        }

        // 分配到商品的继续计算
        return $this->setProductPrice($groupOrderProducts, $orderProductDiscount);
    }
}