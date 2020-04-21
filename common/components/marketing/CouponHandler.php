<?php

namespace addons\TinyShop\common\components\marketing;

use Yii;
use yii\web\UnprocessableEntityHttpException;
use common\helpers\ArrayHelper;
use common\helpers\BcHelper;
use addons\TinyShop\common\models\forms\PreviewForm;
use addons\TinyShop\common\models\marketing\Coupon;
use addons\TinyShop\common\components\PreviewInterface;
use addons\TinyShop\common\enums\RangeTypeEnum;
use addons\TinyShop\common\enums\PreferentialTypeEnum;
use addons\TinyShop\common\models\order\OrderProduct;
use addons\TinyShop\common\enums\ProductMarketingEnum;

/**
 * 优惠券
 *
 * Class CouponHandler
 * @package addons\TinyShop\common\components\marketing
 * @author jianyan74 <751393839@qq.com>
 */
class CouponHandler extends PreviewInterface
{
    /**
     * @param PreviewForm $form
     * @return PreviewForm
     * @throws UnprocessableEntityHttpException
     */
    public function execute(PreviewForm $form): PreviewForm
    {
        if (empty($form->coupon_id)) {
            return $form;
        }

        if (!($coupon = Yii::$app->tinyShopService->marketingCoupon->findByIdWithType($form->coupon_id, $form->member->id))) {
            throw new UnprocessableEntityHttpException('找不到优惠券');
        }

        if ($coupon['state'] == Coupon::STATE_UNSED) {
            throw new UnprocessableEntityHttpException('优惠券已使用');
        }

        if ($coupon['state'] == Coupon::STATE_PAST_DUE) {
            throw new UnprocessableEntityHttpException('优惠券已过期');
        }

        if ($coupon['state'] == Coupon::STATE_GET && (time() <= $coupon['start_time'] || time() >= $coupon['end_time'])) {
            throw new UnprocessableEntityHttpException('优惠券不在有效使用时间内');
        }

        // 校验是否是某些产品可用
        if ($coupon['couponType']['range_type'] ==  RangeTypeEnum::ASSIGN) {
            $couponProductIds = ArrayHelper::getColumn($coupon->couponProduct, 'product_id');
            // 判断是否在可用产品内
            $productIds = array_keys($form->groupOrderProducts);
            // 有效的产品id
            $usableIds = $this->usableVerify($couponProductIds, $productIds);

            // 折扣获取最高价产品进行折扣
            $money = 0;
            $product_id = 0;
            foreach ($form->groupOrderProducts as $key => $groupOrderProduct) {
                if (in_array($key, $usableIds) && ($groupOrderProduct['product_money'] > $money)) {
                    $money = $groupOrderProduct['product_money'];
                    $product_id = $key;
                }
            }

            // 记录营销
            $form->marketingDetails[] = [
                'marketing_id' => $coupon['couponType']['id'],
                'marketing_type' => ProductMarketingEnum::COUPON,
                'marketing_condition' => '满' . $coupon['at_least'] . '元，减' . $coupon['money'],
                'discount_money' => $coupon['money'],
                'product_id' => $product_id,
            ];

            $form->coupon_money = $this->getCouponMoney($money, $coupon);
        } else {
            // 记录营销
            $form->marketingDetails[] = [
                'marketing_id' => $coupon['couponType']['id'],
                'marketing_type' => ProductMarketingEnum::COUPON,
                'marketing_condition' => '满' . $coupon['at_least'] . '元，减' . $coupon['money'],
                'discount_money' => $coupon['money'],
            ];

            $form->coupon_money = $this->getCouponMoney($form->product_money, $coupon);
        }

        $form->coupon = $coupon;

        // 成功触发
        return $this->success($form);
    }

    /**
     * 获取优惠券金额
     *
     * @param double $money 使用金额
     * @param array $coupon 优惠券
     * @return float
     * @throws UnprocessableEntityHttpException
     */
    public function getCouponMoney($money, $coupon)
    {
        if ($money < $coupon['at_least']) {
            throw new UnprocessableEntityHttpException('优惠券最低可使用金额为' . $coupon['at_least']);
        }

        // 满减
        if ($coupon['type'] == PreferentialTypeEnum::MONEY) {
            return $coupon['money'];
        }

        // 满折扣
        return BcHelper::mul(BcHelper::div((100 - $coupon['discount']), 100, 4), $money);
    }

    /**
     * @param array $couponProductIds 优惠券可用产品id
     * @param array $productIds 已有产品id
     * @return array
     * @throws UnprocessableEntityHttpException
     */
    public function usableVerify(array $couponProductIds, array $productIds)
    {
        // 有效的产品id
        $usableIds = [];
        $couponUsable = false;
        foreach ($couponProductIds as $couponProductId) {
            if (in_array($couponProductId, $productIds)) {
                $couponUsable = true;
                $usableIds[] = $couponProductId;
            }
        }

        if ($couponUsable == false) {
            throw new UnprocessableEntityHttpException('该优惠券不可用');
        }

        return $usableIds;
    }

    /**
     * 排斥营销
     *
     * @return array
     */
    public function rejectNames()
    {
        return [];
    }

    /**
     * 营销名称
     *
     * @return string
     */
    public static function getName(): string
    {
        return 'coupon';
    }
}