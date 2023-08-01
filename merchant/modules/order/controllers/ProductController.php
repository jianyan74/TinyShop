<?php

namespace addons\TinyShop\merchant\modules\order\controllers;

use Yii;
use common\helpers\ArrayHelper;
use addons\TinyShop\merchant\controllers\BaseController;
use addons\TinyShop\merchant\modules\order\forms\PriceAdjustmentForm;

/**
 * Class ProductController
 * @package addons\TinyShop\merchant\modules\order\controllers
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
                Yii::$app->services->actionLog->create('order', '调整金额', $order->id);

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
}
