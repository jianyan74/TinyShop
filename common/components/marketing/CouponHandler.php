<?php

namespace addons\TinyShop\common\components\marketing;

use Yii;
use yii\web\UnprocessableEntityHttpException;
use common\enums\UseStateEnum;
use addons\TinyShop\common\forms\PreviewForm;
use addons\TinyShop\common\components\PreviewInterface;
use addons\TinyShop\common\enums\MarketingEnum;

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

        if (!($coupon = Yii::$app->tinyShopService->marketingCoupon->findByMemberId($form->coupon_id, $form->member->id))) {
            throw new UnprocessableEntityHttpException('找不到优惠券');
        }

        if ($coupon['state'] == UseStateEnum::USE) {
            throw new UnprocessableEntityHttpException('优惠券已使用');
        }

        if ($coupon['state'] == UseStateEnum::PAST_DUE) {
            throw new UnprocessableEntityHttpException('优惠券已过期');
        }

        if ($coupon['state'] == UseStateEnum::GET && (time() <= $coupon['start_time'] || time() >= $coupon['end_time'])) {
            throw new UnprocessableEntityHttpException('优惠券不在有效使用时间内');
        }

        if ($coupon['merchant_id'] != 0 && $form->merchant_id != $coupon['merchant_id']) {
            throw new UnprocessableEntityHttpException('无效的优惠券');
        }

        $validCoupon = Yii::$app->tinyShopService->marketingCoupon->getPredictDiscountByCoupon($coupon, $form->groupOrderProducts);
        if ($validCoupon == false) {
            throw new UnprocessableEntityHttpException('优惠券不可用');
        }

        $couponMoney = $validCoupon['predictDiscount'];

        // 记录营销
        $form->marketingDetails[] = [
            'uuid' => $validCoupon['productIds'],
            'marketing_id' => $coupon['coupon_type_id'],
            'marketing_type' => MarketingEnum::COUPON,
            'marketing_condition' => '满' . $coupon['at_least'] . '元，折扣减' . $couponMoney,
            'discount_money' => $couponMoney,
        ];

        if ($validCoupon['predictTotalMoney'] < $coupon['at_least']) {
            throw new UnprocessableEntityHttpException('优惠券需满 ' . $coupon['at_least'] . ' 元才可使用.');
        }

        $form->coupon = $coupon;

        // 成功触发
        return $this->success($form);
    }

    /**
     * @param array $couponProductIds 优惠券可用商品id
     * @param array $productIds 已有商品id
     * @return array
     * @throws UnprocessableEntityHttpException
     */
    public function usableVerify(array $couponProductIds, array $productIds)
    {
        // 有效的商品id
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
