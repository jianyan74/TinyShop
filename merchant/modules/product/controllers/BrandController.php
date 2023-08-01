<?php

namespace addons\TinyShop\merchant\modules\product\controllers;

use Yii;
use yii\web\Response;
use common\enums\StatusEnum;
use common\models\base\SearchModel;
use common\traits\MerchantCurd;
use addons\TinyShop\common\models\product\Brand;
use addons\TinyShop\merchant\controllers\BaseController;

/**
 * Class ProductBrandController
 * @package addons\TinyShop\merchant\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class BrandController extends BaseController
{
    use MerchantCurd;

    /**
     * @var Brand
     */
    public $modelClass = Brand::class;

    /**
     * 首页
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        $searchModel = new SearchModel([
            'model' => Brand::class,
            'scenario' => 'default',
            'partialMatchAttributes' => ['title'], // 模糊查询
            'defaultOrder' => [
                'merchant_id' => SORT_DESC,
                'sort' => SORT_ASC,
                'id' => SORT_DESC,
            ],
            'pageSize' => $this->pageSize,
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->andWhere(['>=', 'status', StatusEnum::DISABLED])
            ->andWhere(['in', 'merchant_id', [0, $this->getMerchantId()]]);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'merchant_id' => Yii::$app->services->merchant->getNotNullId(),
        ]);
    }

    /**
     * @return mixed|string|\yii\console\Response|Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxEdit()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $model = $this->findModel($id);
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load($request->post())) {
            return $model->save()
                ? $this->redirect(Yii::$app->request->referrer)
                : $this->message($this->getError($model), $this->redirect(['index']), 'error');
        }

        return $this->renderAjax($this->action->id, [
            'model' => $model,
            'cates' => Yii::$app->tinyShopService->productCate->getMapList(),
        ]);
    }
}