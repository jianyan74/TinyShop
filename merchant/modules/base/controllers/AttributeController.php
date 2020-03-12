<?php

namespace addons\TinyShop\merchant\modules\base\controllers;

use Yii;
use common\enums\StatusEnum;
use common\models\base\SearchModel;
use common\traits\MerchantCurd;
use addons\TinyShop\common\models\base\Attribute;
use addons\TinyShop\common\models\base\AttributeValue;
use addons\TinyShop\backend\controllers\BaseController;

/**
 * 基础商品类型
 *
 * Class BaseAttributeController
 * @package addons\TinyShop\merchant\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class AttributeController extends BaseController
{
    use MerchantCurd;

    /**
     * @var Attribute
     */
    public $modelClass = Attribute::class;

    /**
     * 首页
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        $searchModel = new SearchModel([
            'model' => Attribute::class,
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
        $model = $this->findModel($id);
        $model->spec_ids = explode(',', $model->spec_ids);
        if ($model->load($request->post())) {
            !empty($model->spec_ids) && $model->spec_ids = implode(',', $model->spec_ids);

            if ($model->save()) {
                return $this->redirect(['index']);
            }
        }

        return $this->render($this->action->id, [
            'model' => $model,
            'specs' => Yii::$app->tinyShopService->baseSpec->getMapList(),
            'valueType' => AttributeValue::$typeExplain,
        ]);
    }
}