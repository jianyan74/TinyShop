<?php

namespace addons\TinyShop\merchant\modules\base\controllers;

use Yii;
use common\enums\StatusEnum;
use common\models\base\SearchModel;
use common\helpers\ResultHelper;
use common\traits\MerchantCurd;
use addons\TinyShop\common\models\base\Spec;
use addons\TinyShop\backend\controllers\BaseController;
use addons\TinyShop\merchant\forms\SpecForm;

/**
 * 规格
 *
 * Class BaseSpecController
 * @package addons\TinyShop\merchant\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class SpecController extends BaseController
{
    use MerchantCurd;

    /**
     * @var Spec
     */
    public $modelClass = Spec::class;

    /**
     * 首页
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['title'], // 模糊查询
            'defaultOrder' => [
                'sort' => SORT_ASC,
                'id' => SORT_DESC,
            ],
            'pageSize' => $this->pageSize,
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->andWhere(['status' => StatusEnum::ENABLED])
            ->andWhere(['merchant_id' => $this->getMerchantId()])
            ->with(['value']);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'showTypeExplain' => Spec::$showTypeExplain,
        ]);
    }

    /**
     * 编辑/创建
     *
     * @return mixed
     */
    public function actionEdit()
    {
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $model = $this->findFormModel($id);
        if ($model->load($request->post()) && $model->save()) {
            return $this->referrer();
        }

        return $this->render($this->action->id, [
            'model' => $model,
            'showTypeExplain' => Spec::$showTypeExplain,
        ]);
    }

    /**
     * 创建
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $title = Yii::$app->request->post('title');
        $show_type = Yii::$app->request->post('show_type');
        $base_attribute_id = Yii::$app->request->post('base_attribute_id');

        $model = new Spec();
        $model = $model->loadDefaultValues();
        $model->show_type = $show_type;
        $model->title = $title;
        if (!$model->save()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        // 关联类型
        $baseAttribute = Yii::$app->tinyShopService->baseAttribute->findById($base_attribute_id);
        $baseAttribute->spec_ids = $baseAttribute->spec_ids . ',' . $model->id;
        $baseAttribute->save();

        return ResultHelper::json(200, '添加成功', $model);
    }

    /**
     * @param $id
     * @return array
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeleteValue($id)
    {
        if (Yii::$app->tinyShopService->productSpecValue->has($id)) {
            return ResultHelper::json(404, '属性已经在使用无法删除');
        }

        return ResultHelper::json(200, '删除成功');
    }

    /**
     * 删除
     *
     * @param $id
     * @return mixed
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        if (Yii::$app->tinyShopService->productSpec->has($id)) {
            return $this->message("规格已经在使用无法删除", $this->redirect(['index']), 'error');
        }

        if ($this->findModel($id)->delete()) {
            return $this->message("删除成功", $this->redirect(['index']));
        }

        return $this->message("删除失败", $this->redirect(['index']), 'error');
    }

    /**
     * @param $id
     * @return mixed|string
     */
    public function actionDestroy($id)
    {
        if (Yii::$app->tinyShopService->productSpec->has($id)) {
            return $this->message("规格已经在使用无法删除", $this->redirect(['index']), 'error');
        }

        if (!($model = $this->modelClass::findOne($id))) {
            return $this->message("找不到数据", $this->redirect(['index']), 'error');
        }

        $model->status = StatusEnum::DELETE;
        if ($model->save()) {
            return $this->message("删除成功", $this->redirect(['index']));
        }

        return $this->message("删除失败", $this->redirect(['index']), 'error');
    }

    /**
     * 返回模型
     *
     * @param $id
     * @return \yii\db\ActiveRecord
     */
    protected function findFormModel($id)
    {
        /* @var $model \yii\db\ActiveRecord */
        if (empty($id) || empty(($model = SpecForm::findOne(['id' => $id, 'merchant_id' => $this->getMerchantId()])))) {
            $model = new SpecForm();
            return $model->loadDefaultValues();
        }

        return $model;
    }
}