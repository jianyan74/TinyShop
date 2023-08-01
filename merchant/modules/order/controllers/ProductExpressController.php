<?php

namespace addons\TinyShop\merchant\modules\order\controllers;

use addons\TinyShop\common\enums\OrderTypeEnum;
use Yii;
use yii\web\NotFoundHttpException;
use common\helpers\ArrayHelper;
use common\helpers\ExcelHelper;
use common\enums\StatusEnum;
use addons\TinyShop\common\models\order\ProductExpress;
use addons\TinyShop\merchant\controllers\BaseController;
use addons\TinyShop\common\enums\OrderStatusEnum;
use addons\TinyShop\common\models\order\Order;
use addons\TinyShop\common\enums\RefundStatusEnum;
use addons\TinyShop\merchant\modules\order\forms\ProductExpressForm;

/**
 * Class ProductExpressController
 * @package addons\TinyShop\merchant\modules\order\controllers
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
        $model = new ProductExpressForm();
        $model = $model->loadDefaultValues();
        $model->order = $order;
        $product = ArrayHelper::toArray($order->product);

        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            $model->order_id = $order->id;
            $model->operator_id = Yii::$app->user->identity->id;
            $model->operator_username = Yii::$app->user->identity->username;
            $model->buyer_id = $order->buyer_id;
            $model->buyer_realname = $order->receiver_realname;
            $model->buyer_mobile = $order->receiver_mobile;

            // 事务
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$model->save()) {
                    throw new NotFoundHttpException($this->getError($model));
                }

                Yii::$app->services->actionLog->create('order', '进行发货', $order->id);

                $transaction->commit();

                return $this->message('修改成功', $this->redirect(Yii::$app->request->referrer));
            } catch (\Exception $e) {
                $transaction->rollBack();

                return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }

        $product = Yii::$app->tinyShopService->orderProductExpress->regroupProduct($product, $order->id);
        $model->express_company_id = $order->express_company_id;

        return $this->renderAjax($this->action->id, [
            'model' => $model,
            'order' => $order,
            'product' => $product,
            'company' => Yii::$app->tinyShopService->expressCompany->getMapList()
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
        ]);
    }

    /**
     * 物流状态
     *
     * @param $id
     * @return string
     */
    public function actionCompany($id)
    {
        $model = ProductExpress::findOne($id);
        try {
            $trace = Yii::$app->services->extendLogistics->query($model->express_no, $model->express_company, $model->buyer_mobile, true);
        } catch (\Exception $e) {
            $trace = [
                [
                    'datetime' => date('Y-m-d H:i:s'),
                    'remark' => $e->getMessage(),
                ]
            ];
        }

        return $this->renderAjax($this->action->id, [
            'trace' => $trace,
        ]);
    }

    /**
     * 一键发货
     *
     * @return bool|string
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \yii\base\ExitException
     */
    public function actionImportDeliver()
    {
        if (Yii::$app->request->isPost) {
            // 事务
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $file = $_FILES['excelFile'];
                if (empty($file['tmp_name'])) {
                    return $this->message('请上传需要导入的文件', $this->redirect(['order/index']), 'error');
                }

                $defaultData = ExcelHelper::import($file['tmp_name'], 2);
                $data = [];
                $expressCompany = [];
                $totalNum = 0;
                $successNum = 0;
                foreach ($defaultData as $datum) {
                    $totalNum++;
                    if (empty($datum[0])) {
                        throw new NotFoundHttpException('请填写订单号');
                    }

                    if (empty($datum[1])) {
                        throw new NotFoundHttpException('请填写快递公司');
                    }

                    if (empty($datum[2])) {
                        throw new NotFoundHttpException('请填写快递单号');
                    }

                    $data[$datum[0]] = [
                        'express_name' => '包裹 - 1',
                        'express_company' => $datum[1],
                        'express_no' => $datum[2],
                    ];

                    $expressCompany[$datum[1]] = $datum[1];
                }

                // 查询所有的快递公司是否存在
                $company = Yii::$app->tinyShopService->expressCompany->findByTitles(array_keys($expressCompany));
                $company = ArrayHelper::arrayKey($company, 'title');
                foreach ($expressCompany as $value) {
                    if (!isset($company[$value])) {
                        throw new NotFoundHttpException('找不到 ' . $value . '快递公司');
                    }
                }

                $orders = Order::find()
                    ->select(['id', 'order_sn', 'order_status', 'buyer_id', 'receiver_realname'])
                    ->where(['in', 'order_sn', array_keys($data)])
                    ->andWhere(['order_status' => OrderStatusEnum::PAY])
                    ->with(['product'])
                    ->all();

                foreach ($orders as $order) {
                    // 发货
                    if (isset($data[$order['order_sn']])) {
                        $productIds = [];
                        foreach ($order['product'] as $item) {
                            if (in_array($item['refund_status'], RefundStatusEnum::deliver())) {
                                $productIds[] = $item['id'];
                            }
                        }

                        // 找不到商品跳出本次发货
                        if (empty($productIds)) {
                            continue;
                        }

                        $model = new ProductExpressForm();
                        $model->order = $order;
                        $model = $model->loadDefaultValues();
                        $model->attributes = $data[$order['order_sn']];
                        $model->express_company_id = $company[$value]['id'];
                        $model->is_batch = true;
                        $model->order_id = $order['id'];
                        $model->buyer_id = $order['buyer_id'];
                        $model->buyer_realname = $order['receiver_realname'];
                        $model->buyer_mobile = $order['receiver_mobile'];
                        $model->shipping_type = StatusEnum::ENABLED;
                        $model->operator_id = Yii::$app->user->identity->id;
                        $model->operator_username = Yii::$app->user->identity->username;
                        $model->order_product_ids = $productIds;
                        if (!$model->save()) {
                            throw new NotFoundHttpException($this->getError($model));
                        }

                        Yii::$app->services->actionLog->create('order', '进行发货', $order['id']);
                        $successNum++;
                    }
                }

                $transaction->commit();

                return $this->message('本次共发货 ' . $totalNum . ' 单, 成功 ' . $successNum . ' 单', $this->redirect(['order/index']));
            } catch (\Exception $e) {
                $transaction->rollBack();

                return $this->message($e->getMessage(), $this->redirect(['order/index']), 'error');
            }
        }

        return $this->renderAjax($this->action->id);
    }

    /**
     * 下载模板
     */
    public function actionDeliverTemplateDownload()
    {
        $path = Yii::getAlias('@addons') . '/TinyShop/common/file/deliver-template.xls';

        Yii::$app->response->sendFile($path, '一键导入订单发货模板_' . date('YmdHis') . '.xls');
    }
}
