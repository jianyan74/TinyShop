<?php

namespace addons\TinyShop\merchant\modules\common\controllers;

use Yii;
use yii\web\UnprocessableEntityHttpException;
use common\enums\StatusEnum;
use common\models\base\SearchModel;
use common\enums\AuditStatusEnum;
use common\enums\AppEnum;
use addons\TinyShop\merchant\controllers\BaseController;
use addons\TinyShop\common\models\common\ProductService;
use addons\TinyShop\common\models\common\ProductServiceMap;

/**
 * Class ProductServiceMapController
 * @package addons\TinyShop\merchant\modules\common\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class ProductServiceMapController extends BaseController
{
    /**
     * @var ProductServiceMap
     */
    public $modelClass = ProductServiceMap::class;

    /**
     * @param \yii\base\Action $action
     * @return bool
     * @throws UnprocessableEntityHttpException
     * @throws \yii\web\BadRequestHttpException
     * @throws \yii\web\UnauthorizedHttpException
     */
    public function beforeAction($action)
    {
        if (in_array($action->id, ['audit', 'pass', 'refuse']) && Yii::$app->id != AppEnum::BACKEND) {
            throw new UnprocessableEntityHttpException('没有权限访问');
        }

        return parent::beforeAction($action);
    }

    /**
     * 首页
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        $models = ProductService::find()
            ->andWhere(['>=', 'status', StatusEnum::DISABLED])
            ->with(['map'])
            ->asArray()
            ->all();

        return $this->render($this->action->id, [
            'models' => $models,
        ]);
    }

    /**
     * @param $service_id
     * @return mixed|string
     */
    public function actionApply($service_id)
    {
        $model = $this->findModel($service_id);
        if ($model->audit_status == AuditStatusEnum::ENABLED) {
            return $this->message("已申请通过", $this->redirect(Yii::$app->request->referrer), 'error');
        }

        $model->service_id = $service_id;
        $model->audit_status = AuditStatusEnum::DISABLED;
        $model->save();

        return $this->message("申请提交成功", $this->redirect(Yii::$app->request->referrer));
    }

    /**
     * 审核首页
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionAudit()
    {
        $auditStatus = Yii::$app->request->get('auditStatus', 0);

        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC,
            ],
            'pageSize' => $this->pageSize,
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->andWhere(['>=', 'status', StatusEnum::DISABLED])
            ->andWhere(['>', 'merchant_id', 0])
            ->andFilterWhere(['audit_status' => $auditStatus])
            ->with(['merchant', 'productService']);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'auditStatus' => $auditStatus
        ]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function actionRefuse($id)
    {
        $model = Yii::$app->tinyShopService->productServiceMap->findById($id);
        if ($model->audit_status != AuditStatusEnum::DISABLED) {
            return $this->message('申请已经被处理', $this->redirect(Yii::$app->request->referrer), 'error');
        }

        $model->audit_status = AuditStatusEnum::DELETE;
        $model->audit_time = time();
        $model->save();

        return $this->message('拒绝申请成功', $this->redirect(Yii::$app->request->referrer));
    }

    /**
     * @param $id
     * @return mixed|string
     */
    public function actionPass($id)
    {
        $model = Yii::$app->tinyShopService->productServiceMap->findById($id);
        if ($model->audit_status != AuditStatusEnum::DISABLED) {
            return $this->message('申请已经被处理', $this->redirect(Yii::$app->request->referrer), 'error');
        }

        $model->audit_status = AuditStatusEnum::ENABLED;
        $model->audit_time = time();
        $model->save();

        return $this->message('申请通过', $this->redirect(Yii::$app->request->referrer));
    }

    /**
     * @param $id
     * @return mixed|string
     */
    public function actionDelete($id)
    {
        $model = Yii::$app->tinyShopService->productServiceMap->findById($id);
        if (!$model) {
            return $this->message('找不到记录', $this->redirect(Yii::$app->request->referrer), 'error');
        }

        $model->delete();

        return $this->message('操作成功', $this->redirect(Yii::$app->request->referrer));
    }

    /**
     * 返回模型
     *
     * @param $id
     * @return \yii\db\ActiveRecord
     */
    protected function findModel($service_id)
    {
        /* @var $model \yii\db\ActiveRecord */
        if (empty($service_id) || empty(($model = $this->modelClass::find()
                ->where(['service_id' => $service_id, 'merchant_id' => $this->getMerchantId()])
                ->one())
            )
        ) {
            $model = new $this->modelClass;
            return $model->loadDefaultValues();
        }

        return $model;
    }
}
