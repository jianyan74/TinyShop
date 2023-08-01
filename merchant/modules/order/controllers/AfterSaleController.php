<?php

namespace addons\TinyShop\merchant\modules\order\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use common\models\base\SearchModel;
use common\forms\CreditsLogForm;
use common\enums\StatusEnum;
use common\enums\PayTypeEnum;
use common\helpers\BcHelper;
use common\helpers\StringHelper;
use common\helpers\ResultHelper;
use addons\TinyShop\common\models\order\AfterSale;
use addons\TinyShop\common\forms\OrderAfterSaleForm;
use addons\TinyShop\common\models\order\Order;
use addons\TinyShop\common\enums\RefundStatusEnum;
use addons\TinyShop\merchant\controllers\BaseController;
use addons\TinyShop\common\enums\SubscriptionActionEnum;
use addons\TinyShop\common\models\order\OrderProduct;
use yii\web\UnprocessableEntityHttpException;

/**
 * 售后
 *
 * Class AfterSaleController
 * @package addons\TinyShop\merchant\modules\order\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class AfterSaleController extends BaseController
{
    /**
     * 首页
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        $afterSale = Yii::$app->request->get('after_sale', StatusEnum::DISABLED);
        $condition = ['in', 'refund_status', RefundStatusEnum::refund()];
        if ($afterSale == StatusEnum::ENABLED) {
            $condition = [
                'in',
                'refund_status',
                [
                    0,
                    RefundStatusEnum::NO_PASS_ALWAYS,
                    RefundStatusEnum::CANCEL,
                    RefundStatusEnum::NO_PASS,
                    RefundStatusEnum::MEMBER_AFFIRM,
                    RefundStatusEnum::CONSENT
                ]
            ];
        }

        $searchModel = new SearchModel([
            'model' => AfterSale::class,
            'scenario' => 'default',
            'partialMatchAttributes' => ['title'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC,
            ],
            'pageSize' => $this->pageSize,
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->andWhere(['>=', 'status', StatusEnum::DISABLED])
            ->andWhere($condition)
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()]);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'afterSale' => $afterSale,
        ]);
    }

    /**
     * 同意退款申请
     *
     * @return array
     */
    public function actionPass()
    {
        $id = Yii::$app->request->post('id');

        try {
            Yii::$app->tinyShopService->orderAfterSale->pass($id);

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
    public function actionRefuse()
    {
        $id = Yii::$app->request->post('id');
        $always = Yii::$app->request->post('always', false);

        try {
            Yii::$app->tinyShopService->orderAfterSale->refuse($id, $always);
            // 取消退款通知
            $afterSale = $this->findRefundModel($id);
            /** @var OrderProduct $orderProduct */
            $orderProduct = $afterSale->orderProduct;
            $orderProduct->product_name = StringHelper::textNewLine($orderProduct->product_name, 15, 1)[0]; // 内容过长无法通知
            Yii::$app->tinyShopService->notify->createRemindByReceiver(
                $afterSale->order_id,
                SubscriptionActionEnum::ORDER_RETURN_APPLY_CLOSE,
                $afterSale->buyer_id,
                [
                    'afterSale' => $afterSale,
                    'orderProduct' => $orderProduct,
                ]
            );

            return ResultHelper::json(200, '操作成功');
        } catch (\Exception $e) {
            return ResultHelper::json(422, $e->getMessage());
        }
    }

    /**
     * 确认收货
     *
     * @return array
     */
    public function actionTakeDelivery()
    {
        $id = Yii::$app->request->post('id');

        try {
            Yii::$app->tinyShopService->orderAfterSale->merchantTakeDelivery($id);

            return ResultHelper::json(200, '操作成功');
        } catch (\Exception $e) {
            return ResultHelper::json(422, $e->getMessage());
        }
    }

    /**
     * 发货
     *
     * @param $id
     * @return mixed|string
     * @throws \yii\base\ExitException
     */
    public function actionDeliver($id)
    {
        $model = Yii::$app->tinyShopService->orderAfterSale->findById($id);
        $order = Yii::$app->tinyShopService->order->findById($model->order_id);

        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            // 事务
            $transaction = Yii::$app->db->beginTransaction();

            try {
                if (!($company = Yii::$app->tinyShopService->expressCompany->findById($model->merchant_express_company_id))) {
                    throw new UnprocessableEntityHttpException('找不到物流公司');
                }

                $model->merchant_express_company = $company['title'];
                Yii::$app->tinyShopService->orderAfterSale->merchantDelivery($model);

                $transaction->commit();

                return $this->message('发货成功', $this->redirect(Yii::$app->request->referrer));
            } catch (\Exception $e) {
                $transaction->rollBack();

                return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }

        $model->merchant_express_company_id = $order->express_company_id;

        return $this->renderAjax($this->action->id, [
            'model' => $model,
            'order' => $order,
            'company' => Yii::$app->tinyShopService->expressCompany->getMapList()
        ]);
    }

    /**
     * 物流状态
     *
     * @param $id
     * @return string
     */
    public function actionCompany($id, $is_merchant = false)
    {
        $model = $this->findRefundModel($id);
        if ($is_merchant == true) {
            $trace = Yii::$app->services->extendLogistics->query($model->merchant_express_no, $model->merchant_express_company, $model->merchant_express_mobile);
        } else {
            $trace = Yii::$app->services->extendLogistics->query($model->member_express_no, $model->member_express_company, $model->member_express_mobile);
        }

        return $this->renderAjax($this->action->id, [
            'trace' => $trace,
        ]);
    }

    /**
     * 确认退款
     *
     * @param $id
     * @return mixed|string
     * @throws NotFoundHttpException
     */
    public function actionAffirmReturn($id)
    {
        $model = $this->findRefundModel($id);
        $model->setScenario('affirmRefund');
        // 申请默认退款金额
        $model->refund_money = $model->refund_apply_money;
        /** @var Order $order */
        $order = $model->order;
        $refundTypes = [
            PayTypeEnum::ON_LINE => '线下',
            PayTypeEnum::USER_MONEY => '余额',
        ];

        // 线上支付
        $thirdParty = PayTypeEnum::thirdParty();
        in_array($order->pay_type, array_keys($thirdParty)) && $refundTypes[$order->pay_type] = $thirdParty[$order->pay_type];
        // 判断默认值
        in_array($order->pay_type, array_keys($refundTypes)) && $model->refund_pay_type = $order->pay_type;
        // 退款上限
        $maxRefundMoney = BcHelper::sub($order->pay_money, $order->refund_money);

        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            // 开启事务
            $transaction = Yii::$app->db->beginTransaction();
            try {
                // 退款进订单
                $model->refund_money > $maxRefundMoney && $model->refund_money = $maxRefundMoney;
                $afterSale = Yii::$app->tinyShopService->orderAfterSale->returnMoney($id, $model->refund_money);
                if ($model->refund_pay_type == PayTypeEnum::USER_MONEY) {
                    // 退款进用户余额/原路退回
                    Yii::$app->services->memberCreditsLog->incrMoney(new CreditsLogForm([
                        'member' => Yii::$app->services->member->get($model->buyer_id),
                        'num' => $afterSale->refund_money,
                        'group' => 'orderRefund',
                        'map_id' => $afterSale->id,
                        'remark' => '订单退款-' . $order->order_sn,
                    ]));
                } elseif (in_array($model->refund_pay_type, array_keys($thirdParty))) {
                    Yii::$app->services->extendPay->refund($model->refund_pay_type, $model->refund_money, $order->order_sn);
                }

                // 自动触发计算订单状态
                Yii::$app->tinyShopService->order->autoUpdateStatus($order->id);
                // 处理判断售后状态
                Yii::$app->tinyShopService->order->autoUpdateAfterSale($order->id);
                // 确认退款通知
                /** @var OrderProduct $orderProduct */
                $orderProduct = $afterSale->orderProduct;
                $orderProduct->product_name = StringHelper::textNewLine($orderProduct->product_name, 15, 1)[0]; // 内容过长无法通知
                Yii::$app->tinyShopService->notify->createRemindByReceiver(
                    $afterSale->order_id,
                    SubscriptionActionEnum::ORDER_RETURN_MONEY,
                    $afterSale->buyer_id,
                    [
                        'afterSale' => $afterSale,
                        'orderProduct' => $orderProduct,
                    ]
                );

                $transaction->commit();

                return $this->message('操作成功', $this->redirect(Yii::$app->request->referrer));
            } catch (\Exception $e) {
                $transaction->rollBack();

                return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }

        return $this->renderAjax($this->action->id, [
            'model' => $model,
            'order' => $order,
            'orderProduct' => $model->orderProduct,
            'refundTypes' => $refundTypes,
            'maxRefundMoney' => $maxRefundMoney,
        ]);
    }

    /**
     * @param $id
     * @return string
     */
    public function actionDetail($id)
    {
        $model = Yii::$app->tinyShopService->orderAfterSale->findById($id);

        return $this->render($this->action->id, [
            'model' => $model,
            'orderAction' => Yii::$app->services->actionLog->findByBehavior('orderAfterSale', $id, 'TinyShop'),
        ]);
    }

    /**
     * @param $id
     * @return OrderAfterSaleForm|\yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    public function findRefundModel($id)
    {
        /* @var $model OrderAfterSaleForm */
        if (empty($id) || !($model = OrderAfterSaleForm::find()->where([
                'id' => $id,
                'status' => StatusEnum::ENABLED,
            ])->andFilterWhere(['merchant_id' => $this->getMerchantId()])->one())) {
            throw new NotFoundHttpException('请求的数据不存在');
        }

        return $model;
    }
}
