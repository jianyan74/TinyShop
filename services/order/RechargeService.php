<?php

namespace addons\TinyShop\services\order;

use Yii;
use common\components\Service;
use common\enums\StatusEnum;
use common\forms\CreditsLogForm;
use yii\web\UnprocessableEntityHttpException;
use addons\TinyShop\common\enums\CouponGetTypeEnum;
use addons\TinyShop\common\models\order\Recharge;

/**
 * Class RechargeService
 * @package addons\TinyShop\services\order
 */
class RechargeService extends Service
{
    /**
     * @param $order_id
     * @return array|null|\yii\db\ActiveRecord|Recharge
     */
    public function findByOrderSn($order_sn)
    {
        return Recharge::find()
            ->where(['order_sn' => $order_sn, 'status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->one();
    }

    /**
     * 支付
     *
     * @param Recharge $order
     * @param int $paymentType 支付类型
     * @throws UnprocessableEntityHttpException
     * @throws \yii\web\NotFoundHttpException
     */
    public function pay(Recharge $order, $member, $paymentType)
    {
        if ($order->pay_status == StatusEnum::ENABLED) {
            throw new UnprocessableEntityHttpException('订单已经被处理');
        }

        $order->pay_status = StatusEnum::ENABLED;
        $order->pay_type = $paymentType;
        $order->pay_time = time();
        $order->save();

        // 赠送积分
        $order->give_point > 0 && Yii::$app->services->memberCreditsLog->incrInt(new CreditsLogForm([
            'member' => $order->member,
            'num' => $order->give_point,
            'group' => 'rechargeGive',
            'map_id' => $order->id,
            'remark' => '在线充值-' . $order->order_sn,
        ]));

        // 赠送成长值
        $order->give_growth > 0 && Yii::$app->services->memberCreditsLog->incrGrowth(new CreditsLogForm([
            'member' => $order->member,
            'num' => $order->give_growth,
            'group' => 'rechargeGive',
            'map_id' => $order->id,
            'remark' => '在线充值-' . $order->order_sn,
        ]));

        // 充值进余额
        $order->give_price > 0 && Yii::$app->services->memberCreditsLog->incrMoney(new CreditsLogForm([
            'member' => Yii::$app->services->member->findById($order->member_id),
            'pay_type' => $paymentType,
            'num' => $order->give_price,
            'group' => 'rechargeGive',
            'remark' => '充值赠送-' . $order->order_sn,
            'map_id' => $order->id,
            'is_give' => true,
        ]));

        // 赠送优惠券
        $couponTypes = Yii::$app->tinyShopService->marketingCouponType->findByIds($order->give_coupon_type_ids);
        foreach ($couponTypes as $couponType) {
            Yii::$app->tinyShopService->marketingCoupon->giveByNewRecord(
                $couponType,
                $member->id,
                $order->id,
                CouponGetTypeEnum::RECHARGE
            );
        }
    }
}
