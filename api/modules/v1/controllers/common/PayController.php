<?php

namespace addons\TinyShop\api\modules\v1\controllers\common;

use Yii;
use yii\web\UnprocessableEntityHttpException;
use common\enums\PayTypeEnum;
use common\helpers\Url;
use common\helpers\ResultHelper;
use common\enums\StatusEnum;
use common\helpers\AddonHelper;
use common\models\forms\CreditsLogForm;
use common\models\forms\PayForm;
use api\controllers\OnAuthController;
use addons\TinyShop\common\models\forms\OrderPayFrom;
use addons\TinyShop\common\models\forms\RechargePayFrom;

/**
 * 公用支付
 *
 * Class PayController
 * @package addons\TinyShop\api\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class PayController extends OnAuthController
{
    /**
     * @var PayForm
     */
    public $modelClass = PayForm::class;

    /**
     * @return array|mixed|\yii\db\ActiveRecord
     * @throws UnprocessableEntityHttpException
     * @throws \EasyWeChat\Kernel\Exceptions\HttpException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \yii\base\InvalidConfigException
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
                'recharge' => RechargePayFrom::class,
                'order' => OrderPayFrom::class,
            ]);
            // 回调方法
            $payForm->notify_url = Url::removeMerchantIdUrl('toFront', ['notify/' . PayTypeEnum::action($payForm->pay_type)]);
            !$payForm->openid && $payForm->openid = Yii::$app->user->identity->openid;
            // 生成配置
            return ResultHelper::json(200, '待支付', [
                'payStatus' => false,
                'config' => $payForm->getConfig(),
            ]);
        }

        /*---------------------------------------------------------------------------*/
        /********************************** 余额支付 *********************************/
        /*---------------------------------------------------------------------------*/

        $config = AddonHelper::getConfig();
        if (!isset($config['order_balance_pay']) || $config['order_balance_pay'] == StatusEnum::DISABLED) {
            throw new UnprocessableEntityHttpException('不支持余额支付');
        }

        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $order_id = $payForm->data['order_id'];
            if (!($order = Yii::$app->tinyShopService->order->findById($order_id))) {
                throw new UnprocessableEntityHttpException('订单不存在');
            }

            // 扣除余额
            $member = Yii::$app->services->member->get($payForm->member_id);
            Yii::$app->services->memberCreditsLog->decrMoney(new CreditsLogForm([
                'member' => $member,
                'num' => $order->pay_money,
                'credit_group' => 'orderCreate',
                'map_id' => $order_id,
                'remark' => '【微商城】订单余额支付',
            ]));

            Yii::$app->tinyShopService->order->pay($order, PayTypeEnum::USER_MONEY);

            // 记录操作
            Yii::$app->tinyShopService->orderAction->create(
                '订单支付',
                $order->id,
                $order->order_status,
                $member->id,
                $member->username
            );

            $transaction->commit();

            return ResultHelper::json(200, '支付成功', [
                'payStatus' => true,
            ]);
        } catch (\Exception $e) {
            $transaction->rollBack();

            return ResultHelper::json(422, $e->getMessage());
        }
    }
}