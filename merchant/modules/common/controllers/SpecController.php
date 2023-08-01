<?php

namespace addons\TinyShop\merchant\modules\common\controllers;

use Yii;
use common\enums\StatusEnum;
use common\models\base\SearchModel;
use common\traits\MerchantCurd;
use common\helpers\ResultHelper;
use addons\TinyShop\common\models\common\Spec;
use addons\TinyShop\merchant\controllers\BaseController;
use addons\TinyShop\merchant\modules\common\forms\SpecForm;

/**
 * Class SpecController
 * @package addons\TinyShop\merchant\modules\common\controllers
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
            ->andWhere(['is_tmp' => StatusEnum::DISABLED])
            ->andWhere(['merchant_id' => $this->getMerchantId()])
            ->with(['value']);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
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
        ]);
    }

    /**
     * 创建
     *
     * @param $title
     * @param $type
     * @return array|mixed
     */
    public function actionCreate($title, $type)
    {
        $model = new Spec();
        $model = $model->loadDefaultValues();
        $model->title = $title;
        $model->type = $type;
        $model->is_tmp = StatusEnum::ENABLED;
        $model->save();

        return ResultHelper::json(200, 'ok', $model);
    }

    /**
     * @return array|mixed
     */
    public function actionSearch($title)
    {
        $data = $this->modelClass::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere(['like', 'title', $title])
            ->orderBy('sort asc')
            ->with(['value'])
            ->limit(8)
            ->asArray()
            ->all();

        return ResultHelper::json(200, 'ok', $data);
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
