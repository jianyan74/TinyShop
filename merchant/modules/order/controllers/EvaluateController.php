<?php

namespace addons\TinyShop\merchant\modules\order\controllers;

use Yii;
use common\traits\MerchantCurd;
use common\enums\StatusEnum;
use common\models\base\SearchModel;
use addons\TinyShop\common\models\product\Evaluate;
use addons\TinyShop\merchant\controllers\BaseController;

/**
 * Class EvaluateController
 * @package addons\TinyShop\merchant\modules\order\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class EvaluateController extends BaseController
{
    use MerchantCurd;

    /**
     * @var Evaluate
     */
    public $modelClass = Evaluate::class;

    /**
     * 首页
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        $searchModel = new SearchModel([
            'model' => Evaluate::class,
            'scenario' => 'default',
            'partialMatchAttributes' => ['title'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC,
            ],
            'pageSize' => $this->pageSize,
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->andWhere(['>=', 'status', StatusEnum::DISABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()]);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * ajax编辑/创建
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxEdit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);

        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            return $model->save()
                ? $this->redirect(['index'])
                : $this->message($this->getError($model), $this->redirect(['index']), 'error');
        }

        return $this->renderAjax($this->action->id, [
            'model' => $model,
            'type' => Yii::$app->request->get('type'),
        ]);
    }

    /**
     * 返回模型
     *
     * @param $id
     * @return \yii\db\ActiveRecord
     */
    protected function findModel($id)
    {
        /* @var $model \yii\db\ActiveRecord */
        if (empty($id) || empty(($model = $this->modelClass::find()->where(['id' => $id])->andFilterWhere(['merchant_id' => $this->getMerchantId()])->one()))) {
            $model = new $this->modelClass;
            return $model->loadDefaultValues();
        }

        return $model;
    }
}
