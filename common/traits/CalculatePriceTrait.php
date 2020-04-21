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

        ($allDiscount - $orderDiscount) > 0 && $this->calculateByOrderProductPrice($form->groupOrderProducts, $orderProductDiscount);
        $orderDiscount > 0 && $this->calculateByOrderPrice($form->groupOrderProducts, $orderDiscount, $form->product_count);
        $form->product_money = BcHelper::sub($form->product_money, $allDiscount);
        $form->product_money < 0 && $form->product_money = 0;
        $form->marketing_money += $this->marketing_money;

        return $form;
    }

    /**
     * 根据总订单产品优惠分配到产品
     *
     * @param $groupOrderProducts
     * @param $orderDiscount
     * @param $productCount
     */
    public function calculateByOrderProductPrice($groupOrderProducts, $orderProductDiscount)
    {
        /** @var OrderProduct $orderProduct */
        foreach ($groupOrderProducts as $product_id => &$groupOrderProduct) {
            if (!isset($orderProductDiscount[$product_id]) || $orderProductDiscount[$product_id] == 0) {
                continue;
            }

            // 产品总优惠
            $discount = $orderProductDiscount[$product_id];
            $productMoney = $groupOrderProduct['product_money'];
            $groupOrderProduct['product_money'] -= $discount;
            // 如果优惠价格过大
            if ($groupOrderProduct['product_money'] <= 0) {
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
            }

            foreach ($groupOrderProduct['products'] as $orderProduct) {
                if ($orderProduct->product_money == 0) {
                    continue;
                }

                // 每个产品数量的优惠
                $preferentialPrice = BcHelper::div($discount, $groupOrderProduct['count']);
                // 临时的优惠价格
                if ($preferentialPrice == 0) {
                    $tmpPreferentialPrice = $preferentialPrice;
                } else {
                    $tmpPreferentialPrice = $preferentialPrice * $orderProduct->num;
                }

                // 如果优惠价格大于产品价格，采用产品价格
                if ($tmpPreferentialPrice > $orderProduct->product_money) {
                    $tmpPreferentialPrice = $orderProduct->product_money;
                }

                // 分配到产品优惠
                $orderProduct->product_money = $orderProduct->product_money - $tmpPreferentialPrice;
                if ($orderProduct->product_money > 0) {
                    $orderProduct->price = BcHelper::div($orderProduct->product_money, $orderProduct->num);
                }

                $orderProductDiscount[$product_id] -= $tmpPreferentialPrice;
                if ($orderProductDiscount[$product_id] <= 0) {
                    continue;
                }
            }
        }

        $product_money = ArrayHelper::getColumn($groupOrderProducts, 'product_money');
        if (array_sum($product_money) <= 0) {
            return true;
        }

        // 如果优惠的价格还有继续循环
        if (array_sum(array_values($orderProductDiscount)) > 0) {
            return $this->calculateByOrderProductPrice($groupOrderProducts, $orderProductDiscount);
        }

        return true;
    }

    /**
     * 根据总订单优惠分配到产品
     *
     * @param $groupOrderProducts
     * @param $orderDiscount
     * @param $productCount
     */
    public function calculateByOrderPrice($groupOrderProducts, $orderDiscount, $productCount)
    {
        /** @var OrderProduct $orderProduct */
        if ($orderDiscount <= 0 || $productCount == 0) {
            return true;
        }

        // 如果总优惠大于等于总金额 全部设置为0
        $productMoney = array_sum(ArrayHelper::getColumn($groupOrderProducts, 'product_money'));
        if ($productMoney <= 0) {
            return true;
        }

        if ($orderDiscount >= $productMoney) {
            // 记录营销金额
            $this->marketing_money = $productMoney;

            foreach ($groupOrderProducts as $product_id => &$groupOrderProduct) {
                foreach ($groupOrderProduct['products'] as $orderProduct) {
                    $orderProduct->price = 0;
                    $orderProduct->product_money = 0;
                }
            }

            return true;
        } else {
            // 记录营销金额
            $this->marketing_money = $orderDiscount;
        }

        // 每个产品的优惠价
        $preferentialPrice = BcHelper::div($orderDiscount, $productCount);
        $preferentialPrice == 0 && $preferentialPrice = $orderDiscount;

        // 总优惠小于总金额
        foreach ($groupOrderProducts as $product_id => &$groupOrderProduct) {
            if ($groupOrderProduct['product_money'] == 0) {
                continue;
            }

            if ($preferentialPrice == 0) {
                break;
            }

            foreach ($groupOrderProduct['products'] as $orderProduct) {
                // 获取每个产品的优惠金额
                $discount = $orderProduct->num * $preferentialPrice;
                if ($discount >= $orderDiscount) {
                    $discount = $orderDiscount;
                }

                // 产品金额大于优惠金额
                if ($orderProduct->product_money > $discount) {
                    $orderDiscount -= $discount;
                    $orderProduct->product_money -= $discount;
                    $orderProduct->price = BcHelper::div($orderProduct->product_money, $orderProduct->num);
                    $groupOrderProduct['product_money'] -= $discount;
                } else {
                    $orderDiscount -= $orderProduct->product_money;
                    $groupOrderProduct['product_money'] -= $orderProduct->product_money;
                    $orderProduct->price = 0;
                    $orderProduct->product_money = 0;
                    $productCount -= $orderProduct->num;
                }
            }
        }

        // 如果优惠的价格还有继续循环
        if ($orderDiscount > 0) {
            return $this->calculateByOrderPrice($groupOrderProducts, $orderDiscount, $productCount);
        }

        return true;
    }
}