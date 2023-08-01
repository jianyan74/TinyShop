<?php

namespace addons\TinyShop\api\modules\v1\controllers\member;

use Yii;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use api\controllers\UserAuthController;
use common\helpers\BcHelper;
use common\enums\PayTypeEnum;
use common\enums\StatusEnum;
use common\helpers\ResultHelper;
use addons\TinyShop\common\forms\OrderSearchForm;
use addons\TinyShop\common\models\order\Order;
use addons\TinyShop\common\enums\OrderStatusEnum;
use addons\TinyShop\common\enums\ShippingTypeEnum;

/**
 * Class OrderController
 * @package addons\TinyShop\api\modules\v1\controllers\member
 * @author jianyan74 <751393839@qq.com>
 */
class OrderController extends UserAuthController
{
    /**
     * @var Order
     */
    public $modelClass = Order::class;

    /**
     * 首页
     *
     * @return array|ActiveDataProvider|\yii\db\ActiveRecord[]
     */
    public function actionIndex()
    {
        $model = new OrderSearchForm();
        $model->attributes = Yii::$app->request->get();
        $model->member_id = Yii::$app->user->identity->member_id;

        return Yii::$app->tinyShopService->order->query($model);
    }

    /**
     * 单个显示
     *
     * @param $id
     * @return \yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $with = ['product', 'coupon', 'baseMerchant', 'marketingDetail'];
        // 简单的查询订单基本信息
        if ($simplify = Yii::$app->request->get('simplify')) {
            $with = [];
        }

        $model = $this->modelClass::find()->where([
            'id' => $id,
            'status' => StatusEnum::ENABLED,
            'buyer_id' => Yii::$app->user->identity->member_id,
        ])
            ->with($with)
            ->asArray()
            ->one();

        if (!$model) {
            throw new NotFoundHttpException('找不到订单信息');
        }

        // 自提地点
        $model['store'] = $model['shipping_type'] == ShippingTypeEnum::PICKUP ? Yii::$app->tinyShopService->orderStore->findById($id) : [];
        // 合并营销显示
        $model['marketingDetail'] = Yii::$app->tinyShopService->marketing->mergeIdenticalMarketing($model['marketingDetail'] ?? []);
        // 支付类型、配送方式
        $model['pay_explain'] = PayTypeEnum::getValue($model['pay_type']);
        $model['shipping_explain'] = ShippingTypeEnum::getValue($model['shipping_type']);
        // 好友代付(未支付的情况)
        $model['peer_pay'] = [];

        // 调价
        $model['adjust_money'] = 0;
        if (!empty($model['product'])) {
            foreach ($model['product'] as $value) {
                // 调整金额
                $model['adjust_money'] = BcHelper::add($model['adjust_money'], $value['adjust_money']);
            }
        }

        $setting = Yii::$app->tinyShopService->config->setting();
        $model['order_invoice_status'] = $setting->order_invoice_status;

        return $model;
    }

    /**
     * 关闭订单
     *
     * @param $id
     * @throws NotFoundHttpException
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function actionClose($id)
    {
        $member_id = Yii::$app->user->identity->member_id;
        // 记录操作
        Yii::$app->services->actionLog->create('order', '用户关闭订单', $id);

        return Yii::$app->tinyShopService->order->close($id, $member_id);
    }

    /**
     * 删除订单
     *
     * @param $id
     * @throws NotFoundHttpException
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        // 非关闭订单不可删除
        if ($model->order_status != OrderStatusEnum::REPEAL) {
            return ResultHelper::json(422, "删除失败");
        }

        $model->status = StatusEnum::DELETE;
        if ($model->save()) {
            // 记录操作
            Yii::$app->services->actionLog->create('order', '删除订单', $model->id);

            return true;
        }

        return ResultHelper::json(422, "删除失败");
    }

    /**
     * 确认收货
     *
     * @param $id
     * @throws NotFoundHttpException
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function actionTakeDelivery($id)
    {
        $member_id = Yii::$app->user->identity->member_id;
        $data = Yii::$app->tinyShopService->order->takeDelivery($id, $member_id);

        return $data;
    }

    /**
     * @param $id
     * @return \yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        /* @var $model \yii\db\ActiveRecord */
        if (empty($id) || !($model = $this->modelClass::find()->where([
                'id' => $id,
                'status' => StatusEnum::ENABLED,
                'buyer_id' => Yii::$app->user->identity->member_id
            ])->andFilterWhere(['merchant_id' => $this->getMerchantId()])->one())) {
            throw new NotFoundHttpException('请求的数据不存在或您的权限不足.');
        }

        return $model;
    }

    /**
     * 权限验证
     *
     * @param string $action 当前的方法
     * @param null $model 当前的模型类
     * @param array $params $_GET变量
     * @throws \yii\web\BadRequestHttpException
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        // 方法名称
        if (in_array($action, ['update', 'create'])) {
            throw new \yii\web\BadRequestHttpException('权限不足');
        }
    }
}
