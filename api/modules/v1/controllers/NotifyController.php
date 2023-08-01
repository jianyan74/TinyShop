<?php

namespace addons\TinyShop\api\modules\v1\controllers;

use Yii;
use common\traits\PayNotify;
use common\enums\PayTypeEnum;
use common\helpers\ArrayHelper;
use common\forms\CreditsLogForm;
use common\models\extend\PayLog;
use api\controllers\OnAuthController;
use addons\TinyShop\common\enums\ShippingTypeEnum;
use addons\TinyShop\common\models\order\Order;
use addons\TinyShop\common\enums\PayGroupEnum;

/**
 * Class NotifyController
 * @package addons\TinyShop\api\modules\v1\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class NotifyController extends OnAuthController
{
    use PayNotify;

    public $modelClass = '';

    /**
     * 不用进行登录验证的方法
     *
     * 例如： ['index', 'update', 'create', 'view', 'delete']
     * 默认全部需要验证
     *
     * @var array
     */
    protected $authOptional = ['wechat', 'alipay', 'union', 'byte-dance', 'stripe'];

    /**
     * @param $action
     * @return bool
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\BadRequestHttpException
     * @throws \yii\web\ForbiddenHttpException
     */
    public function beforeAction($action)
    {
        // 非格式化返回
        Yii::$app->params['triggerBeforeSend'] = false;

        return parent::beforeAction($action);
    }

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
                Yii::$app->tinyShopService->order->pay($order, $log->pay_type, true);
                // 记录消费日志
                $member = Yii::$app->services->member->findById($log->member_id);
                Yii::$app->services->memberCreditsLog->consumeMoney(new CreditsLogForm([
                    'member' => $member,
                    'num' => $log->pay_fee,
                    'group' => 'orderPay',
                    'pay_type' => $log->pay_type,
                    'remark' => "订单支付-" . $order->order_sn,
                    'map_id' => $log->id,
                ]));

                // 订单收货
                if ($order->shipping_type == ShippingTypeEnum::TO_STORE) {
                    Yii::$app->tinyShopService->order->virtualTakeDelivery($order);
                }
                break;
            case PayGroupEnum::ORDER_UNITE :
                $member = Yii::$app->services->member->get($log['member_id']);
                /** @var Order $order */
                $order = Yii::$app->tinyShopService->order->findByOrderSn($log->order_sn);

                // 充值
                $this->recharge($member, $log);
                // 扣除余额
                Yii::$app->services->memberCreditsLog->decrMoney(new CreditsLogForm([
                    'member' => Yii::$app->services->member->findById($log->member_id),
                    'num' => $order->pay_money,
                    'group' => 'orderPay',
                    'map_id' => $order->id,
                    'remark' => '订单支付-' . $order->order_sn,
                    'is_consume' => true
                ]));

                // 支付订单
                Yii::$app->tinyShopService->order->pay($order, PayTypeEnum::USER_MONEY, true);
                break;
            case PayGroupEnum::ORDER_BATCH :
                // 待支付列表
                $orders = Yii::$app->tinyShopService->order->findByUniteNo($log->order_sn);
                /** @var Order $order */
                foreach ($orders as $order) {
                    // 建立新的关联
                    $logModel = new PayLog();
                    $logModel->attributes = ArrayHelper::toArray($log);
                    $logModel->pay_fee = $order->pay_money;
                    $logModel->total_fee = $order->pay_money;
                    $logModel->order_sn = $order->order_sn;
                    $logModel->save();

                    // 记录消费日志
                    Yii::$app->services->memberCreditsLog->consumeMoney(new CreditsLogForm([
                        'member' => Yii::$app->services->member->findById($logModel->member_id),
                        'num' => $logModel->pay_fee,
                        'group' => 'orderPay',
                        'pay_type' => $logModel->pay_type,
                        'remark' => "订单支付-" . $logModel->order_sn,
                        'map_id' => $logModel->id,
                    ]));

                    // 订单支付
                    Yii::$app->tinyShopService->order->pay($order, $logModel->pay_type, true);
                    // 批量支付交易单号
                    Order::updateAll(['out_trade_no' => $log->order_sn], ['id' => $order->id]);
                }
                break;
            case PayGroupEnum::ORDER_UNITE_BATCH :
                $member = Yii::$app->services->member->get($log['member_id']);
                // 充值
                $this->recharge($member, $log);
                // 待支付列表
                $orders = Yii::$app->tinyShopService->order->findByUniteNo($log->order_sn);
                /** @var Order $order */
                foreach ($orders as $order) {
                    // 建立新的关联
                    $logModel = new PayLog();
                    $logModel->attributes = ArrayHelper::toArray($log);
                    $logModel->pay_fee = $order->pay_money;
                    $logModel->total_fee = $order->pay_money;
                    $logModel->order_sn = $order->order_sn;
                    $logModel->save();

                    // 扣除余额
                    Yii::$app->services->memberCreditsLog->decrMoney(new CreditsLogForm([
                        'member' => Yii::$app->services->member->findById($log->member_id),
                        'num' => $logModel->pay_fee,
                        'group' => 'orderPay',
                        'map_id' => $logModel->id,
                        'remark' => '订单支付-' . $logModel->order_sn,
                        'is_consume' => true
                    ]));

                    // 订单支付
                    Yii::$app->tinyShopService->order->pay($order, PayTypeEnum::USER_MONEY, true);
                    // 批量支付交易单号
                    Order::updateAll(['out_trade_no' => $log->order_sn], ['id' => $order['id']]);
                }
                break;
            case PayGroupEnum::RECHARGE :
                $member = Yii::$app->services->member->get($log['member_id']);
                $order = Yii::$app->tinyShopService->orderRecharge->findByOrderSn($log->order_sn);
                Yii::$app->tinyShopService->orderRecharge->pay($order, $member, $log->pay_type);

                // 充值
                $this->recharge($member, $log);
                break;
        }
    }

    /**
     * 充值
     *
     * @param $member
     * @param $log
     * @param $payFee
     * @throws \yii\web\NotFoundHttpException
     */
    public function recharge($member, $log)
    {
        // 充值
        Yii::$app->services->memberCreditsLog->incrMoney(new CreditsLogForm([
            'member' => $member,
            'pay_type' => $log['pay_type'],
            'num' => $log['pay_fee'],
            'group' => 'recharge',
            'remark' => "在线充值-" . $log['out_trade_no'],
            'map_id' => $log['id'],
        ]));
    }
}
