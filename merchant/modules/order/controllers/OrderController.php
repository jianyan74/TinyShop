<?php

namespace addons\TinyShop\merchant\modules\order\controllers;

use Yii;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;
use common\enums\StatusEnum;
use common\enums\PayTypeEnum;
use common\forms\CreditsLogForm;
use common\helpers\ArrayHelper;
use common\helpers\ResultHelper;
use addons\TinyShop\common\enums\OrderStatusEnum;
use addons\TinyShop\common\models\order\Order;
use addons\TinyShop\merchant\controllers\BaseController;
use addons\TinyShop\merchant\modules\order\forms\OrderSearchForm;
use addons\TinyShop\common\enums\SubscriptionActionEnum;
use addons\TinyShop\common\enums\RefundStatusEnum;
use addons\TinyShop\common\models\product\Product;
use addons\TinyShop\merchant\modules\order\forms\OrderStoreForm;
use addons\TinyShop\merchant\modules\order\forms\ProductExpressForm;
use addons\TinyShop\common\enums\ProductExpressShippingTypeEnum;

/**
 * Class OrderController
 * @package addons\TinyShop\merchant\modules\order\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class OrderController extends BaseController
{
    /**
     * @var Order
     */
    public $modelClass = Order::class;

    /**
     * 首页
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $search = new OrderSearchForm();
        $search->attributes = Yii::$app->request->get();
        // 退款中
        if ($search->order_status == OrderStatusEnum::REFUND_ING) {
            $search->order_status = '';
            $search->is_after_sale = StatusEnum::ENABLED;
        }

        $data = Order::find()
            ->alias('o')
            ->where(['>=', 'status', StatusEnum::DISABLED])
            ->andFilterWhere(['pay_type' => $search->pay_type])
            ->andFilterWhere(['order_from' => $search->order_from])
            ->andFilterWhere(['order_type' => $search->order_type])
            ->andFilterWhere(['shipping_type' => $search->shipping_type])
            ->andFilterWhere(['is_after_sale' => $search->is_after_sale])
            ->andFilterWhere(['marketing_id' => $search->marketing_id])
            ->andFilterWhere(['marketing_type' => $search->marketing_type])
            ->andFilterWhere(['wholesale_record_id' => $search->wholesale_record_id])
            ->andFilterWhere(['store_id' => Yii::$app->params['store_id']])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->andFilterWhere($search->getBetweenTime())
            ->andFilterWhere($search->getKeyword())
            ->andFilterWhere($search->getKeyword())
            ->with(['member', 'product'])->andFilterWhere(['order_status' => $search->order_status]);

        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => $this->pageSize]);
        $models = $data->offset($pages->offset)
            ->orderBy('id desc')
            ->limit($pages->limit)
            ->all();

        return $this->render($this->action->id, [
            'models' => $models,
            'pages' => $pages,
            'search' => $search,
            'total' => Yii::$app->tinyShopService->order->findCount(),
            'receiptPrinter' => Yii::$app->services->extendPrinter->findAllAuto(Yii::$app->services->merchant->getNotNullId()),
        ]);
    }

    /**
     * @param $id
     * @return string
     */
    public function actionDetail($id)
    {
        /** @var Order $model */
        $model = Order::find()
            ->where(['id' => $id])
            // ->andWhere(['>=', 'status', StatusEnum::DISABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->with(['member', 'invoice', 'product', 'expressCompany', 'productExpress', 'marketingDetail', 'payLog'])
            ->one();

        $product = ArrayHelper::toArray($model->product);
        $productExpress = ArrayHelper::toArray($model->productExpress);
        // 重组发货
        foreach ($productExpress as &$express) {
            $express['product'] = [];

            foreach ($product as $key => $item) {
                if (in_array($item['id'], $express['order_product_ids'])) {
                    $express['product'][] = $item;
                    unset($product[$key]);
                }
            }
        }

        // 合并营销显示
        $marketingDetails = Yii::$app->tinyShopService->marketing->mergeIdenticalMarketing($model['marketingDetail'] ?? []);

        return $this->render($this->action->id, [
            'model' => $model,
            'product' => $product,
            'productExpress' => $productExpress,
            'marketingDetails' => $marketingDetails,
            'orderAction' => Yii::$app->services->actionLog->findByBehavior('order', $id, 'TinyShop'),
        ]);
    }

    /**
     * 线下支付
     *
     * @param $id
     * @return mixed
     */
    public function actionPay($id)
    {
        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $order = $this->findModel($id);
            Yii::$app->tinyShopService->order->pay($order, PayTypeEnum::OFFLINE);

            $transaction->commit();

            return $this->message('线下支付成功', $this->redirect(Yii::$app->request->referrer));
        } catch (\Exception $e) {
            $transaction->rollBack();

            return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
        }
    }

    /**
     * 确认收货
     *
     * @param $id
     * @return array
     */
    public function actionTakeDelivery($id)
    {
        try {
            Yii::$app->tinyShopService->order->takeDelivery($id);

            return ResultHelper::json(200, '操作成功');
        } catch (\Exception $e) {
            return ResultHelper::json(422, $e->getMessage());
        }
    }

    /**
     * 提货
     *
     * @param $id
     * @return mixed|string
     * @throws NotFoundHttpException
     * @throws \yii\base\ExitException
     */
    public function actionPickup($id)
    {
        $order = $this->findModel($id);
        $model = OrderStoreForm::findOne(['order_id' => $order->id]);

        // ajax校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            // 进行无物流状态
            $productExpressForm = new ProductExpressForm();
            $productExpressForm->order = $order;
            $productExpressForm->order_id = $order->id;
            $productExpressForm->operator_id = Yii::$app->user->identity->id;
            $productExpressForm->operator_username = Yii::$app->user->identity->username;
            $productExpressForm->shipping_type = ProductExpressShippingTypeEnum::NOT_LOGISTICS;
            $productExpressForm->order_product_ids = ArrayHelper::getColumn($order->product, 'id');

            // 事务
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$model->save()) {
                    throw new NotFoundHttpException($this->getError($model));
                }

                if (!$productExpressForm->save()) {
                    throw new NotFoundHttpException($this->getError($productExpressForm));
                }

                // 进行收货
                Yii::$app->tinyShopService->order->takeDelivery($id);

                $transaction->commit();

                return $this->message('提货成功', $this->redirect(Yii::$app->request->referrer));
            } catch (\Exception $e) {
                $transaction->rollBack();

                return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }

        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * 收货地址
     *
     * @param $id
     * @return mixed|string
     * @throws NotFoundHttpException
     * @throws \yii\base\ExitException
     */
    public function actionAddress($id)
    {
        $model = $this->findModel($id);
        $model->setScenario('address');

        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            $model->receiver_name = Yii::$app->services->provinces->getCityListName([
                $model->receiver_province_id,
                $model->receiver_city_id,
                $model->receiver_area_id,
            ]);

            if ($model->save()) {
                Yii::$app->services->actionLog->create('order', '修改收货地址', $model->id);

                return $this->message('修改成功', $this->redirect(Yii::$app->request->referrer));
            }

            return $this->message($this->getError($model), $this->redirect(Yii::$app->request->referrer), 'error');
        }

        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * 备注
     *
     * @param $id
     * @return mixed|string
     * @throws NotFoundHttpException
     * @throws \yii\base\ExitException
     */
    public function actionSellerMemo($id)
    {
        $model = $this->findModel($id);

        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                // 记录操作
                Yii::$app->services->actionLog->create('order', '修改备注', $model->id);

                return $this->message('修改成功', $this->redirect(Yii::$app->request->referrer));
            }

            return $this->message($this->getError($model), $this->redirect(Yii::$app->request->referrer), 'error');
        }

        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * 关闭订单
     *
     * @param $id
     * @return array
     */
    public function actionClose($id)
    {
        try {
            Yii::$app->tinyShopService->order->close($id);
            // 记录操作
            Yii::$app->services->actionLog->create('orderClose', '管理员关闭订单', $id);

            return ResultHelper::json(200, '操作成功');
        } catch (\Exception $e) {
            return ResultHelper::json(422, $e->getMessage());
        }
    }

    /**
     * 订单退单
     *
     * @param $id
     * @return array
     */
    public function actionChargeback($id)
    {
        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();
        try {
            /** @var Order $order */
            $order = $this->findModel($id);
            if (!empty($order->productExpress)) {
                throw new UnprocessableEntityHttpException('部分商品已发货，不支持退单');
            }

            $isCommission = false;
            // 原路退回
            $orderProducts = Yii::$app->tinyShopService->orderProduct->findByOrderId($id);
            foreach ($orderProducts as $orderProduct) {
                if ($orderProduct['order_status'] != OrderStatusEnum::PAY) {
                    throw new UnprocessableEntityHttpException($orderProduct['product_name'] . '不处于支付状态');
                }

                if ($orderProduct['refund_money'] > 0) {
                    throw new UnprocessableEntityHttpException($orderProduct['product_name'] . '已退款');
                }

                if (!in_array($orderProduct['refund_status'], RefundStatusEnum::deliver())) {
                    throw new UnprocessableEntityHttpException($orderProduct['product_name'] . '退款状态未处理');
                }

                // 关闭分销
                if ($orderProduct['is_commission'] == StatusEnum::ENABLED) {
                    $isCommission = true;
                }
            }

            // 退款进用户余额/原路退回
            if ($order->pay_type == PayTypeEnum::USER_MONEY) {
                Yii::$app->services->memberCreditsLog->incrMoney(new CreditsLogForm([
                    'member' => Yii::$app->services->member->findById($order->buyer_id),
                    'num' => $order->pay_money,
                    'group' => 'orderRefund',
                    'map_id' => $order->id,
                    'remark' => '订单退单-' . $order->order_sn,
                ]));
            } elseif (in_array($order->pay_type, array_keys(PayTypeEnum::thirdParty()))) {
                Yii::$app->services->extendPay->refund($order->pay_type, $order->pay_money, $order->order_sn);
            }

            $isCommission == true && Yii::$app->tinyDistributeService->promoterOrder->closeAll($order->order_sn, 'order', 'TinyShop');
            // 增加本身订单退款金额
            Order::updateAllCounters(['refund_money' => $order->pay_money], ['id' => $order->id]);
            // 关闭订单
            Yii::$app->tinyShopService->order->close($order->id);
            // 退单提醒用户
            Yii::$app->tinyShopService->notify->createRemindByReceiver(
                $order->id,
                SubscriptionActionEnum::ORDER_CANCEL,
                $order->buyer_id,
                ['order' => $order]
            );
            // 记录日志
            Yii::$app->services->actionLog->create('order', '退单', $id);

            $transaction->commit();

            return ResultHelper::json(200, '操作成功');
        } catch (\Exception $e) {
            $transaction->rollBack();

            return ResultHelper::json(422, $e->getMessage());
        }
    }

    /**
     * 伪删除
     *
     * @param $id
     * @return mixed
     */
    public function actionDestroy($id)
    {
        $model = $this->modelClass::findOne($id);

        // 非关闭订单不可删除
        if ($model->order_status != OrderStatusEnum::REPEAL) {
            return $this->message("删除失败", $this->redirect(['index']), 'error');
        }

        $model->status = StatusEnum::DELETE;
        if ($model->save()) {
            Yii::$app->services->actionLog->create('order', '删除订单', $id);
            return $this->message("删除成功", $this->redirect(['index']));
        }

        return $this->message("删除失败", $this->redirect(['index']), 'error');
    }

    /**
     * 打单标记
     *
     * @param $id
     * @param $status
     * @return array|mixed|string
     */
    public function actionPrintRecord($id, $status)
    {
        $this->modelClass::updateAll(['is_print' => $status], ['id' => $id]);

        return $this->message('操作成功', $this->redirect(Yii::$app->request->referrer));
    }

    /**
     * @param $id
     * @return array|mixed
     */
    public function actionPrintReceipt($id, $config_id)
    {
        if (!($model = $this->modelClass::findOne($id))) {
            return ResultHelper::json(422, '找不到订单');
        }

        try {
            Yii::$app->tinyShopService->order->printReceipt($model, $config_id);

            return ResultHelper::json(200, '打印成功');
        } catch (\Exception $e) {
            return ResultHelper::json(422, $e->getMessage());
        }
    }

    /**
     * Class print delivery
     * @param $id
     * @return mixed
     */
    public function actionPrintDelivery($id)
    {
        /** @var Order $model */
        $model = Order::find()
            ->where(['>=', 'status', StatusEnum::DISABLED])
            ->andWhere(['id' => $id])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->with(['member', 'product', 'expressCompany'])
            ->one();

        $product = $model->product;
        $total = 0;
        $sku = [];
        foreach ($product as $key => $detail) {
            if (in_array($detail['refund_status'], RefundStatusEnum::deliver())) {
                $sku[$key] = Product::find()
                    ->where(['id' => $detail['product_id']])
                    ->with('cate')
                    ->asArray()
                    ->one();
                $sku[$key]['num'] = $detail['num'];
                $sku[$key]['sku_name'] = $detail['sku_name'];
                $sku[$key]['product_money'] = $detail['product_money'];
                $sku[$key]['refund_status'] = $detail['refund_status'];

                $total += $detail['num'];
            }
        }

        ArrayHelper::multisort($sku, ['tags'], [SORT_ASC]);

        return $this->render($this->action->id, [
            'model' => $model,
            'product' => $sku,
            'total' => $total,
            'productMoney' => $model->product_money - $model->refund_money,
        ]);
    }

    /**
     * @param $id
     * @return Order
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        /* @var $model Order */
        if (empty($id) || empty($model = $this->modelClass::find()
                ->where(['id' => $id])
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->one())
        ) {
            throw new NotFoundHttpException('找不到订单');
        }

        return $model;
    }
}
