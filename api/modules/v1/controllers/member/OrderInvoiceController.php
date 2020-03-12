<?php

namespace addons\TinyShop\api\modules\v1\controllers\member;

use Yii;
use api\controllers\UserAuthController;
use addons\TinyShop\common\models\order\Invoice;
use addons\TinyShop\api\modules\v1\forms\InvoiceForm;
use addons\TinyShop\common\models\order\Order;
use common\helpers\ResultHelper;

/**
 * 订单发票
 *
 * Class OrderInvoiceController
 * @package addons\TinyShop\api\modules\v1\controllers\member
 * @author jianyan74 <751393839@qq.com>
 */
class OrderInvoiceController extends UserAuthController
{
    /**
     * @var Invoice
     */
    public $modelClass = Invoice::class;

    /**
     * @return Invoice|array|mixed|\yii\db\ActiveRecord
     */
    public function actionCreate()
    {
        /* @var $model InvoiceForm */
        $model = new InvoiceForm();
        $model->attributes = Yii::$app->request->post();
        $model->member_id = Yii::$app->user->identity->member_id;
        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        if (Yii::$app->tinyShopService->orderInvoice->findByOrderId($model->order_id)) {
            return ResultHelper::json(422, '已申请发票，不可重新补发票');
        }

        /** @var Order $order */
        $order = Yii::$app->tinyShopService->order->findById($model->order_id);
        // 创建发票记录
        $orderInvoice = Yii::$app->tinyShopService->orderInvoice->create($order, $model->invoice, $model->invoice_content);
        // 关联发票编号
        Order::updateAll(['invoice_id' => $model->id], ['id' => $model->order_id]);

        return $orderInvoice;
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