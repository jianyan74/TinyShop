<?php

namespace addons\TinyShop\frontend\controllers;

use Yii;
use common\models\common\PayLog;
use common\traits\PayNotify;
use common\enums\PayGroupEnum;
use common\models\forms\CreditsLogForm;

/**
 * 支付回调
 *
 * Class NotifyController
 * @package addons\TinyShop\frontend\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class NotifyController extends BaseController
{
    use PayNotify;

    /**
     * 关闭csrf
     *
     * @var bool
     */
    public $enableCsrfValidation = false;

    /**
     * 支付回调
     *
     * @param PayLog $log
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function notify(PayLog $log)
    {
        $log->pay_ip = Yii::$app->request->userIP;
        $log->save();

        switch ($log->order_group) {
            case PayGroupEnum::ORDER :
                $order = Yii::$app->tinyShopService->order->findByOrderSn($log->order_sn);
                Yii::$app->tinyShopService->order->pay($order, $log->pay_type);

                // 记录消费日志
                Yii::$app->services->memberCreditsLog->consumeMoney(new CreditsLogForm([
                    'member' => Yii::$app->services->member->get($log->member_id),
                    'num' => $log->pay_fee,
                    'credit_group' => 'orderPay',
                    'pay_type' => $log->pay_type,
                    'remark' => "【微商城】订单支付",
                    'map_id' => $log->id,
                ]));

                break;
            case PayGroupEnum::RECHARGE :
                $payFee = $log['pay_fee'];
                $member = Yii::$app->services->member->get($log['member_id']);

                // 充值
                Yii::$app->services->memberCreditsLog->incrMoney(new CreditsLogForm([
                    'member' => $member,
                    'pay_type' => $log['pay_type'],
                    'num' => $payFee,
                    'credit_group' => 'recharge',
                    'remark' => "【微商城】在线充值",
                    'map_id' => $log['id'],
                ]));

                // 赠送
                if (($money = Yii::$app->services->memberRechargeConfig->getGiveMoney($payFee)) > 0) {
                    Yii::$app->services->memberCreditsLog->giveMoney(new CreditsLogForm([
                        'member' => $member,
                        'pay_type' => $log['pay_type'],
                        'num' => $money,
                        'credit_group' => 'rechargeGive',
                        'remark' => "【微商城】充值赠送",
                        'map_id' => $log['id'],
                    ]));
                }

                break;
        }
    }
}