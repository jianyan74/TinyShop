<?php

namespace addons\TinyShop\merchant\modules\order\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use common\helpers\ArrayHelper;
use addons\TinyShop\merchant\forms\DeliverProductForm;
use addons\TinyShop\common\models\order\ProductExpress;
use addons\TinyShop\merchant\controllers\BaseController;

/**
 * Class OrderProductExpressController
 * @package addons\TinyShop\merchant\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class ProductExpressController extends BaseController
{
    /**
     * @param $id
     * @return mixed|string
     * @throws \yii\base\ExitException
     */
    public function actionCreate($id)
    {
        $order = Yii::$app->tinyShopService->order->findById($id);
        $model = new DeliverProductForm();
        $model = $model->loadDefaultValues();
        $model->order = $order;
        $product = ArrayHelper::toArray($order->product);

        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            $model->buyer_id = $order->buyer_id;
            $model->member_id = Yii::$app->user->identity->id;
            $model->member_username = Yii::$app->user->identity->username;

            // 事务
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$model->save()) {
                    throw new NotFoundHttpException($this->getError($model));
                }

                // 记录操作
                Yii::$app->tinyShopService->orderAction->create(
                    '进行发货',
                    $order->id,
                    $order->order_status,
                    Yii::$app->user->identity->id,
                    Yii::$app->user->identity->username
                );

                $transaction->commit();

                return $this->message('修改成功', $this->redirect(Yii::$app->request->referrer));
            } catch (\Exception $e) {
                $transaction->rollBack();

                return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }

        $product = Yii::$app->tinyShopService->orderProductExpress->regroupProduct($product, $order->id);
        $model->express_company_id = $order->company_id;

        return $this->renderAjax($this->action->id, [
            'model' => $model,
            'order' => $order,
            'product' => $product,
            'company' => Yii::$app->tinyShopService->expressCompany->getMapList(),
            'shippingTypeExplain' => ProductExpress::$shippingTypeExplain,
        ]);
    }

    /**
     * @param $id
     * @return mixed|string
     * @throws \yii\base\ExitException
     */
    public function actionUpdate($id)
    {
        $model = ProductExpress::findOne($id);

        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            return $model->save()
                ? $this->message('修改成功', $this->redirect(Yii::$app->request->referrer))
                : $this->message($this->getError($model), $this->redirect(Yii::$app->request->referrer), 'error');
        }

        return $this->renderAjax($this->action->id, [
            'model' => $model,
            'order' => $model->order,
            'company' => Yii::$app->tinyShopService->expressCompany->getMapList(),
            'shippingTypeExplain' => ProductExpress::$shippingTypeExplain,
        ]);
    }

}