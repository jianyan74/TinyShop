<?php

namespace addons\TinyShop\merchant\modules\order\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use common\helpers\ResultHelper;
use common\enums\PayTypeEnum;
use common\models\forms\CreditsLogForm;
use addons\TinyShop\common\models\order\OrderProduct;
use addons\TinyShop\common\models\forms\RefundForm;
use addons\TinyShop\merchant\forms\PriceAdjustmentForm;
use addons\TinyShop\merchant\controllers\BaseController;
use addons\TinyShop\common\models\order\Order;

/**
 * Class OrderProductController
 * @package addons\TinyShop\merchant\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class ProductController extends BaseController
{
    /**
     * 调价
     *
     * @param $id
     * @return string
     * @throws \yii\base\ExitException
     */
    public function actionPriceAdjustment($id)
    {
        $model = new PriceAdjustmentForm();
        $order = Yii::$app->tinyShopService->order->findById($id);
        $model->shipping_money = $order->shipping_money;

        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            // 事务
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model->save($order);

                // 记录操作
                Yii::$app->tinyShopService->orderAction->create(
                    '调整金额',
                    $order->id,
                    $order->order_status,
                    Yii::$app->user->identity->id,
                    Yii::$app->user->identity->username
                );

                $transaction->commit();

                return $this->message('调价成功', $this->redirect(Yii::$app->request->referrer));
            } catch (\Exception $e) {
                $transaction->rollBack();

                return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }

        return $this->renderAjax($this->action->id, [
            'order' => $order,
            'product' => ArrayHelper::toArray($order->product),
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return string
     */
    public function actionRefundDetail($id)
    {
        return $this->render($this->action->id, [
            'model' => Yii::$app->tinyShopService->orderProduct->findById($id),
        ]);
    }

    /**
     * 同意退款申请
     *
     * @return array
     */
    public function actionRefundPass()
    {
        $id = Yii::$app->request->post('id');

        try {
            Yii::$app->tinyShopService->orderProduct->refundPass($id);

            return ResultHelper::json(200, '操作成功');
        } catch (\Exception $e) {
            return ResultHelper::json(422, $e->getMessage());
        }
    }

    /**
     * 拒绝退款申请
     *
     * @param $id
     * @param $always
     * @return array
     */
    public function actionRefundNoPass()
    {
        $id = Yii::$app->request->post('id');
        $always = Yii::$app->request->post('always', false);

        try {
            Yii::$app->tinyShopService->orderProduct->refundNoPass($id, $always);

            return ResultHelper::json(200, '操作成功');
        } catch (\Exception $e) {
            return ResultHelper::json(422, $e->getMessage());
        }
    }

    /**
     * 确认退款
     *
     * @param $id
     * @return mixed|string
     * @throws NotFoundHttpException
     */
    public function actionRefundReturnMoney($id)
    {
        /** @var OrderProduct $model */
        $model = $this->findRefundModel($id);
        /** @var Order $order */
        $order = $model->order;
        $refundTypes = [];
        $refundTypes[PayTypeEnum::ON_LINE] = '线下';
        $refundTypes[PayTypeEnum::USER_MONEY] = '余额';
        $defaultRefundType = PayTypeEnum::ON_LINE;
        // 线上支付
        $thirdParty = PayTypeEnum::thirdParty();
        if (in_array($order->payment_type, array_keys($thirdParty))) {
            $refundTypes[$order->payment_type] = $thirdParty[$order->payment_type];
        }

        // 判断默认值
        if (in_array($order->payment_type, array_keys($refundTypes))) {
            $defaultRefundType = $order->payment_type;
        }

        // 申请默认退款金额
        $model->refund_balance_money = Yii::$app->tinyShopService->orderProduct->getRefundBalanceMoney($order, $model);

        if ($model->load(Yii::$app->request->post())) {
            // 开启事务
            $transaction = Yii::$app->db->beginTransaction();
            try {
                // 退款进订单
                $orderProduct = Yii::$app->tinyShopService->orderProduct->refundReturnMoney($id);
                if ($model->refund_type == PayTypeEnum::USER_MONEY) {
                    // 退款进用户余额/原路退回
                    Yii::$app->services->memberCreditsLog->incrMoney(new CreditsLogForm([
                        'member' => Yii::$app->services->member->get($model->member_id),
                        'num' => $orderProduct->refund_balance_money,
                        'credit_group' => 'orderRefundBalanceMoney',
                        'map_id' => $orderProduct->id,
                        'remark' => '【微商城】订单退款',
                    ]));
                } elseif (in_array($model->refund_type, array_keys($thirdParty))) {
                    Yii::$app->services->pay->refund($model->refund_type, $orderProduct->refund_balance_money, $order->order_sn);
                }

                $transaction->commit();

                return $this->message('操作成功', $this->redirect(Yii::$app->request->referrer));
            } catch (\Exception $e) {
                $transaction->rollBack();

                return $this->message($e->getMessage(), $this->redirect(['order/index']), 'error');
            }
        }

        return $this->renderAjax($this->action->id, [
            'product' => $model,
            'order' => $order,
            'refundTypes' => $refundTypes,
            'defaultRefundType' => $defaultRefundType,
        ]);
    }

    /**
     * 确认收货
     *
     * @return array
     */
    public function actionRefundDelivery()
    {
        $id = Yii::$app->request->post('id');

        try {
            Yii::$app->tinyShopService->orderProduct->refundDelivery($id);

            return ResultHelper::json(200, '操作成功');
        } catch (\Exception $e) {
            return ResultHelper::json(422, $e->getMessage());
        }
    }

    /**
     * @param $id
     * @return RefundForm|\yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    public function findRefundModel($id)
    {
        /* @var $model RefundForm */
        if (empty($id) || !($model = RefundForm::find()->where([
                'id' => $id,
                'status' => StatusEnum::ENABLED,
            ])->andWhere(['merchant_id' => $this->getMerchantId()])->one())) {
            throw new NotFoundHttpException('请求的数据不存在');
        }

        return $model;
    }
}