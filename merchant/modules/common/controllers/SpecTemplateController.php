<?php

namespace addons\TinyShop\merchant\modules\common\controllers;

use Yii;
use common\traits\MerchantCurd;
use common\enums\StatusEnum;
use common\helpers\ResultHelper;
use common\models\base\SearchModel;
use addons\TinyShop\common\models\common\SpecTemplate;
use addons\TinyShop\merchant\controllers\BaseController;

/**
 * Class SpecTemplateController
 * @package addons\TinyShop\merchant\modules\common\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class SpecTemplateController extends BaseController
{
    use MerchantCurd;

    /**
     * @var SpecTemplate
     */
    public $modelClass = SpecTemplate::class;

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
            ->andWhere(['merchant_id' => $this->getMerchantId()]);

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
                ? $this->redirect(Yii::$app->request->referrer)
                : $this->message($this->getError($model), $this->redirect(Yii::$app->request->referrer), 'error');
        }

        return $this->renderAjax($this->action->id, [
            'model' => $model,
            'specs' => Yii::$app->tinyShopService->spec->getMap(),
        ]);
    }

    /**
     * @return array|mixed
     */
    public function actionDetails($id)
    {
        $data = $this->modelClass::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere(['id' => $id])
            ->one();

        if (empty($data)) {
            return ResultHelper::json(200, 'ok', []);
        }

        $spec = Yii::$app->tinyShopService->spec->findByIds($data->spec_ids);
        foreach ($spec as &$value) {
            $value['pitch_on_count'] = 0;
        }

        return ResultHelper::json(200, 'ok', $spec);
    }
}
