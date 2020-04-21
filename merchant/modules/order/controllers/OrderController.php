<?php

namespace addons\TinyShop\merchant\modules\order\controllers;

use Yii;
use yii\data\Pagination;
use yii\db\ActiveQuery;
use yii\web\NotFoundHttpException;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use common\helpers\ResultHelper;
use common\enums\PayTypeEnum;
use addons\TinyShop\common\models\order\Order;
use addons\TinyShop\common\enums\OrderStatusEnum;
use addons\TinyShop\common\enums\RefundStatusEnum;
use addons\TinyShop\merchant\forms\DeliverProductForm;
use addons\TinyShop\merchant\forms\PickupForm;
use addons\TinyShop\merchant\controllers\BaseController;

/**
 * Class OrderController
 * @package addons\TinyShop\merchant\controllers
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
        $order_status = Yii::$app->request->get('order_status', '');
        $order_sn = Yii::$app->request->get('order_sn');

        $data = Order::find()
            ->alias('o')
            ->where(['>=', 'o.status', StatusEnum::DISABLED])
            ->andFilterWhere(['like', 'order_sn', $order_sn])
            ->andFilterWhere(['o.merchant_id' => $this->getMerchantId()]);

        if ($order_status != RefundStatusEnum::CANCEL) {
            $data = $data->with(['member', 'product'])->andFilterWhere(['o.order_status' => $order_status]);
        } else {
            // 退款中
            $data = $data->with(['member'])->joinWith([
                'product p' => function (ActiveQuery $query) {
                    return $query->andWhere(['in', 'refund_status', RefundStatusEnum::refund()]);
                },
            ]);
        }

        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => $this->pageSize]);
        $models = $data->offset($pages->offset)
            ->orderBy('id desc')
            ->limit($pages->limit)
            ->all();

        return $this->render($this->action->id, [
            'models' => $models,
            'pages' => $pages,
            'order_status' => $order_status,
            'order_sn' => $order_sn,
            'total' => Yii::$app->tinyShopService->order->getCount(),
        ]);
    }

    /**
     * @param $id
     * @return string
     */
    public function actionDetail($id)
    {
        $model = Order::find()
            ->where(['>=', 'status', StatusEnum::DISABLED])
            ->andWhere(['id' => $id])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->with(['member', 'product', 'company', 'invoice', 'pickup', 'productExpress'])
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

        return $this->render($this->action->id, [
            'model' => $model,
            'product' => $product,
            'productExpress' => $productExpress,
            'orderAction' => Yii::$app->tinyShopService->orderAction->findByOrderId($id),
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
            return $model->save()
                ? $this->message('修改成功', $this->redirect(Yii::$app->request->referrer))
                : $this->message($this->getError($model), $this->redirect(Yii::$app->request->referrer), 'error');
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

        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            $model->receiver_region_name = Yii::$app->services->provinces->getCityListName([
                $model->receiver_province,
                $model->receiver_city,
                $model->receiver_area,
            ]);

            return $model->save()
                ? $this->message('修改成功', $this->redirect(Yii::$app->request->referrer))
                : $this->message($this->getError($model), $this->redirect(Yii::$app->request->referrer), 'error');
        }

        return $this->renderAjax($this->action->id, [
            'model' => $model,
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
        try {
            $order = $this->findModel($id);
            Yii::$app->tinyShopService->order->pay($order, PayTypeEnum::OFFLINE);

            // 记录操作
            Yii::$app->tinyShopService->orderAction->create(
                '订单支付',
                $order->id,
                $order->order_status,
                Yii::$app->user->identity->id,
                Yii::$app->user->identity->username
            );

            return $this->message('线下支付成功', $this->redirect(Yii::$app->request->referrer));
        } catch (\Exception $e) {
            return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
        }
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
            Yii::$app->tinyShopService->orderAction->create(
                '关闭订单',
                $id,
                OrderStatusEnum::NOT_PAY,
                Yii::$app->user->identity->id,
                Yii::$app->user->identity->username
            );

            return ResultHelper::json(200, '操作成功');
        } catch (\Exception $e) {
            return ResultHelper::json(422, $e->getMessage());
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

            // 记录操作
            Yii::$app->tinyShopService->orderAction->create(
                '确认收货',
                $id,
                OrderStatusEnum::SHIPMENTS,
                Yii::$app->user->identity->id,
                Yii::$app->user->identity->username
            );

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
        $model = PickupForm::findOne(['order_id' => $order->id]);

        // ajax校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            // 进行无物流状态
            $deliverProduct = new DeliverProductForm();
            $deliverProduct->order = $order;
            $deliverProduct->member_id = Yii::$app->user->identity->id;
            $deliverProduct->member_username = Yii::$app->user->identity->username;
            $deliverProduct->shipping_type = DeliverProductForm::SHIPPING_TYPE_NOT_LOGISTICS;
            $deliverProduct->order_product_ids = ArrayHelper::getColumn($order->product, 'id');

            // 事务
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$model->save()) {
                    throw new NotFoundHttpException($this->getError($model));
                }

                if (!$deliverProduct->save()) {
                    throw new NotFoundHttpException($this->getError($deliverProduct));
                }

                // 进行收货
                Yii::$app->tinyShopService->order->takeDelivery($id);
                // 记录操作
                Yii::$app->tinyShopService->orderAction->create(
                    '确认收货',
                    $id,
                    OrderStatusEnum::SHIPMENTS,
                    Yii::$app->user->identity->id,
                    Yii::$app->user->identity->username
                );

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
     * 伪删除
     *
     * @param $id
     * @return mixed
     */
    public function actionDestroy($id)
    {
        if (!($model = $this->modelClass::findOne($id))) {
            return $this->message("找不到订单", $this->redirect(['index']), 'error');
        }

        // 非关闭订单不可删除
        if ($model->order_status != OrderStatusEnum::REPEAL) {
            return $this->message("删除失败", $this->redirect(['index']), 'error');
        }

        $model->status = StatusEnum::DELETE;
        if ($model->save()) {
            return $this->message("删除成功", $this->redirect(['index']));
        }

        return $this->message("删除失败", $this->redirect(['index']), 'error');
    }

    /**
     * @param $id
     * @return Order
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        /* @var $model Order */
        if (empty($id) || empty($model = $this->modelClass::find()->where(['id' => $id])->andFilterWhere(['merchant_id' => $this->getMerchantId()])->one())) {
            throw new NotFoundHttpException('找不到订单');
        }

        return $model;
    }
}