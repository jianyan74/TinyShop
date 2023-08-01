<?php

namespace addons\TinyShop\api\modules\v1\controllers\member;

use Yii;
use yii\web\NotFoundHttpException;
use common\helpers\ResultHelper;
use api\controllers\UserAuthController;
use addons\TinyShop\common\enums\OrderStatusEnum;
use addons\TinyShop\common\forms\OrderAfterSaleForm;
use addons\TinyShop\common\models\order\AfterSale;

/**
 * 售后
 *
 * Class OrderAfterSaleController
 * @package addons\TinyShop\api\modules\v1\controllers\member
 * @author jianyan74 <751393839@qq.com>
 */
class OrderAfterSaleController extends UserAuthController
{
    /**
     * @var AfterSale
     */
    public $modelClass = AfterSale::class;

    /**
     * 退款申请
     *
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function actionApply()
    {
        $model = new OrderAfterSaleForm();
        $model->setScenario('apply');
        $model->attributes = Yii::$app->request->post();
        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        $product = Yii::$app->tinyShopService->orderProduct->findById($model->id);
        empty($model->refund_apply_money) && $model->refund_apply_money = $product->product_money;
        if ($model->refund_apply_money > $product->product_money) {
            $model->refund_apply_money = $product->product_money;
        }

        if (
            Yii::$app->services->devPattern->isB2B2C() &&
            $product->order &&
            in_array($product->order->order_status, [
                OrderStatusEnum::REPEAL,
                OrderStatusEnum::ACCOMPLISH,
                OrderStatusEnum::REFUND,
            ])
        ) {
            return ResultHelper::json(422, '完成订单暂不支持售后');
        }

        $member = Yii::$app->services->member->get(Yii::$app->user->identity->member_id);

        return Yii::$app->tinyShopService->orderAfterSale->apply($model, $member->id);
    }

    /**
     * 退货提交
     *
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function actionSalesReturn()
    {
        $model = new OrderAfterSaleForm();
        $model->setScenario('salesReturn');
        $model->attributes = Yii::$app->request->post();
        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        $member = Yii::$app->services->member->get(Yii::$app->user->identity->member_id);

        return Yii::$app->tinyShopService->orderAfterSale->salesReturn($model, $member->id);
    }

    /**
     * 换货确认收货
     *
     * @return array
     */
    public function actionTakeDelivery()
    {
        $id = Yii::$app->request->post('id');

        try {
            return Yii::$app->tinyShopService->orderAfterSale->memberTakeDelivery($id, Yii::$app->user->identity->member_id);
        } catch (\Exception $e) {
            return ResultHelper::json(422, $e->getMessage());
        }
    }

    /**
     * 关闭申请
     *
     * @return mixed|void
     * @throws NotFoundHttpException
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function actionClose()
    {
        $id = Yii::$app->request->post('id');
        try {
            return Yii::$app->tinyShopService->orderAfterSale->close($id, Yii::$app->user->identity->member_id);
        } catch (\Exception $e) {
            return ResultHelper::json(422, $e->getMessage());
        }
    }
}
