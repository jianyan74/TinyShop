<?php

namespace addons\TinyShop\merchant\modules\order\controllers;

use Yii;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use common\enums\StatusEnum;
use common\helpers\ResultHelper;
use common\models\forms\CreditsLogForm;
use addons\TinyShop\common\models\order\Customer;
use addons\TinyShop\common\models\forms\CustomerRefundForm;
use addons\TinyShop\common\models\order\OrderProduct;
use addons\TinyShop\merchant\controllers\BaseController;

/**
 * 售后
 *
 * Class CustomerController
 * @package addons\TinyShop\merchant\modules\order\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class CustomerController extends BaseController
{
    /**
     * 首页
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        $order_sn = Yii::$app->request->get('order_sn');

        $data = Customer::find()
            ->where(['>=', 'status', StatusEnum::DISABLED])
            ->andFilterWhere(['like', 'order_sn', $order_sn])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()]);

        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => $this->pageSize]);
        $models = $data->offset($pages->offset)
            ->orderBy('id desc')
            ->limit($pages->limit)
            ->all();

        return $this->render($this->action->id, [
            'models' => $models,
            'pages' => $pages,
            'order_sn' => $order_sn,
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
            Yii::$app->tinyShopService->orderCustomer->refundPass($id);

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
            Yii::$app->tinyShopService->orderCustomer->refundNoPass($id, $always);

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
        $order = $model->order;

        // 申请默认退款金额
        $model->refund_balance_money = Yii::$app->tinyShopService->orderProduct->getRefundBalanceMoney($order, $model);

        if ($model->load(Yii::$app->request->post())) {
            // 开启事务
            $transaction = Yii::$app->db->beginTransaction();
            try {
                // 退款进订单
                $orderProduct = Yii::$app->tinyShopService->orderCustomer->refundReturnMoney($id, false);
                // 退款进用户余额/原路退回
                Yii::$app->services->memberCreditsLog->incrMoney(new CreditsLogForm([
                    'member' => Yii::$app->services->member->get($model->member_id),
                    'num' => $orderProduct->refund_balance_money,
                    'credit_group' => 'orderRefundBalanceMoney',
                    'map_id' => $orderProduct->id,
                    'remark' => '订单售后退款',
                ]));

                $transaction->commit();

                return $this->message('操作成功', $this->redirect(Yii::$app->request->referrer));
            } catch (\Exception $e) {
                $transaction->rollBack();

                return $this->message($e->getMessage(), Yii::$app->request->referrer, 'error');
            }
        }

        return $this->renderAjax('@addons/TinyShop/merchant/modules/order/views/product/refund-return-money', [
            'product' => $model,
            'order' => $order,
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
            Yii::$app->tinyShopService->orderCustomer->refundDelivery($id);

            return ResultHelper::json(200, '操作成功');
        } catch (\Exception $e) {
            return ResultHelper::json(422, $e->getMessage());
        }
    }

    /**
     * @param $id
     * @return string
     */
    public function actionRefundDetail($id)
    {
        return $this->render($this->action->id, [
            'model' => Yii::$app->tinyShopService->orderCustomer->findById($id),
        ]);
    }

    /**
     * @param $id
     * @return CustomerRefundForm|\yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    public function findRefundModel($id)
    {
        /* @var $model CustomerRefundForm */
        if (empty($id) || !($model = CustomerRefundForm::find()->where([
                'id' => $id,
                'status' => StatusEnum::ENABLED,
            ])->andFilterWhere(['merchant_id' => $this->getMerchantId()])->one())) {
            throw new NotFoundHttpException('请求的数据不存在');
        }

        return $model;
    }
}