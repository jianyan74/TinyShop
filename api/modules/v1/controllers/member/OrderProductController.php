<?php

namespace addons\TinyShop\api\modules\v1\controllers\member;

use Yii;
use yii\web\NotFoundHttpException;
use api\controllers\UserAuthController;
use common\helpers\ResultHelper;
use addons\TinyShop\common\models\order\OrderProduct;
use addons\TinyShop\common\models\forms\RefundForm;
use addons\TinyShop\common\enums\RefundStatusEnum;

/**
 * Class OrderProductController
 * @package addons\TinyShop\api\modules\v1\controllers\member
 * @author jianyan74 <751393839@qq.com>
 */
class OrderProductController extends UserAuthController
{
    /**
     * @var OrderProduct
     */
    public $modelClass = OrderProduct::class;

    /**
     * @return array|\yii\data\ActiveDataProvider|\yii\db\ActiveRecord[]
     */
    public function actionIndex()
    {
        $order_id = Yii::$app->request->get('order_id');
        $is_evaluate = Yii::$app->request->get('is_evaluate');

        return $this->modelClass::find()
            ->where([
                'order_id' => $order_id,
                'member_id' => Yii::$app->user->identity->member_id
            ])
            ->andWhere(['in', 'refund_status', RefundStatusEnum::evaluate()])
            ->andFilterWhere(['is_evaluate' => $is_evaluate])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->asArray()
            ->all();
    }

    /**
     * 退款申请
     *
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function actionRefundApply()
    {
        $model = new RefundForm();
        $model->setScenario('apply');
        $model->attributes = Yii::$app->request->post();
        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        $product = Yii::$app->tinyShopService->orderProduct->findById($model->id);
        empty($model->refund_require_money) && $model->refund_require_money = $product->product_money;
        if ($model->refund_require_money > $product->product_money) {
            $model->refund_require_money = $product->product_money;
        }

        $member = Yii::$app->services->member->get(Yii::$app->user->identity->member_id);

        return Yii::$app->tinyShopService->orderProduct->refundApply($model, $member->id, $member->nickname);
    }

    /**
     * 退货提交
     *
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function actionRefundSalesReturn()
    {
        $model = new RefundForm();
        $model->setScenario('salesReturn');
        $model->attributes = Yii::$app->request->post();
        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        $member = Yii::$app->services->member->get(Yii::$app->user->identity->member_id);

        return Yii::$app->tinyShopService->orderProduct->refundSalesReturn($model, $member->id, $member->nickname);
    }

    /**
     * 关闭申请
     *
     * @return mixed|void
     * @throws NotFoundHttpException
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function actionRefundClose()
    {
        $model = new RefundForm();
        $model->attributes = Yii::$app->request->post();
        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        $member = Yii::$app->services->member->get(Yii::$app->user->identity->member_id);

        return Yii::$app->tinyShopService->orderProduct->refundClose($model->id, $member->id, $member->nickname);
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
        if (in_array($action, ['delete', 'update', 'create'])) {
            throw new \yii\web\BadRequestHttpException('权限不足');
        }
    }
}