<?php

namespace addons\TinyShop\api\modules\v1\controllers\common;

use Yii;
use yii\web\UnprocessableEntityHttpException;
use common\forms\PayForm;
use common\helpers\Url;
use common\helpers\ResultHelper;
use common\enums\PayTypeEnum;
use common\enums\StatusEnum;
use common\forms\CreditsLogForm;
use api\controllers\OnAuthController;
use addons\TinyShop\common\forms\OrderBatchPayFrom;
use addons\TinyShop\common\forms\OrderPayFrom;
use addons\TinyShop\common\forms\OrderUnitePayFrom;
use addons\TinyShop\common\forms\OrderUniteBatchPayFrom;
use addons\TinyShop\common\forms\RechargePayFrom;
use addons\TinyShop\common\enums\PayGroupEnum;
use addons\TinyShop\common\models\order\Order;

/**
 * Class PayController
 * @package addons\TinyShop\api\modules\v1\controllers\common
 */
class PayController extends OnAuthController
{
    /**
     * @var PayForm
     */
    public $modelClass = PayForm::class;

    /**
     * @return array|mixed|\yii\db\ActiveRecord
     */
    public function actionCreate()
    {
        /* @var $payForm PayForm */
        $payForm = new $this->modelClass();
        $payForm->attributes = Yii::$app->request->post();
        $payForm->member_id = Yii::$app->user->identity->member_id;
        $payForm->code = Yii::$app->request->get('code');
        if (!$payForm->validate()) {
            return ResultHelper::json(422, $this->getError($payForm));
        }

        // 非余额支付
        if ($payForm->pay_type != PayTypeEnum::USER_MONEY) {
            // 执行方法
            $payForm->setHandlers([
                // 订单
                'order' => OrderPayFrom::class, // 订单
                'orderUnite' => OrderUnitePayFrom::class, // 订单混合支付
                'orderBatch' => OrderBatchPayFrom::class, // 订单批量支付
                'orderUniteBatch' => OrderUniteBatchPayFrom::class, // 订单批量混合支付
                // 其他
                'recharge' => RechargePayFrom::class, // 充值
            ]);

            // 回调方法
            $payForm->notify_url = Url::removeMerchantIdUrl('toApi', ['v1/notify/' . PayTypeEnum::action($payForm->pay_type)]);
            // $payForm->return_url = '支付宝PC/H5跳转地址';

            // 生成配置
            return ResultHelper::json(200, '待支付', [
                'payStatus' => false,
                'config' => $payForm->getConfig(),
            ]);
        }

        /*---------------------------------------------------------------------------*/
        /********************************** 余额支付 *********************************/
        /*---------------------------------------------------------------------------*/

        $config = Yii::$app->tinyShopService->config->setting();
        if ($config->order_balance_pay == StatusEnum::DISABLED) {
            throw new UnprocessableEntityHttpException('不支持余额支付');
        }

        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();
        try {
            switch ($payForm->order_group) {
                // 订单支付
                case PayGroupEnum::ORDER ;
                    /** @var Order $order */
                    if (
                        empty($payForm->data['order_id']) ||
                        empty($order = Yii::$app->tinyShopService->order->findById($payForm->data['order_id']))
                    ) {
                        throw new UnprocessableEntityHttpException('找不到订单');
                    }

                    $this->balancePay($payForm, $order, $payForm->order_group);
                    break;
                // 订单批量支付
                case PayGroupEnum::ORDER_BATCH ;
                    if (empty($payForm->data['unite_no'])) {
                        throw new UnprocessableEntityHttpException('找不到订单');
                    }

                    $orders = Yii::$app->tinyShopService->order->findByUniteNo($payForm->data['unite_no']);
                    /** @var Order $order */
                    foreach ($orders as $order) {
                        Yii::$app->services->merchant->setId('');
                        $this->balancePay($payForm, $order, $payForm->order_group);
                    }
                    break;
                default :
                    throw new UnprocessableEntityHttpException('订单类型错误');
            }

            $transaction->commit();

            return ResultHelper::json(200, '支付成功', [
                'payStatus' => true,
            ]);
        } catch (\Exception $e) {
            $transaction->rollBack();

            return ResultHelper::json(422, YII_DEBUG ? Yii::$app->services->base->getErrorInfo($e) : $e->getMessage());
        }
    }

    /**
     * @param PayForm $payForm
     * @param Order $order
     * @param $order_group
     * @throws UnprocessableEntityHttpException
     * @throws \yii\web\NotFoundHttpException
     */
    protected function balancePay(PayForm $payForm, Order $order, $order_group)
    {
        $pay_money = $order->pay_money;

        // 扣除余额
        $member = Yii::$app->services->member->findById($payForm->member_id);
        Yii::$app->services->memberCreditsLog->decrMoney(new CreditsLogForm([
            'member' => $member,
            'num' => $pay_money,
            'group' => 'orderCreate',
            'map_id' => $order->id,
            'remark' => '订单支付-' . $order->order_sn,
            'is_consume' => true,
        ]));

        Yii::$app->tinyShopService->order->pay($order, PayTypeEnum::USER_MONEY);
    }
}
