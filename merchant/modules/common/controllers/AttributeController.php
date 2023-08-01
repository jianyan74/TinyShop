<?php

namespace addons\TinyShop\merchant\modules\common\controllers;

use Yii;
use common\enums\StatusEnum;
use common\models\base\SearchModel;
use common\traits\MerchantCurd;
use addons\TinyShop\common\models\common\Attribute;
use addons\TinyShop\merchant\controllers\BaseController;
use addons\TinyShop\merchant\modules\common\forms\AttributeForm;

/**
 * 商品参数
 *
 * Class AttributeController
 * @package addons\TinyShop\merchant\modules\common\controllers
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
        $id = Yii::$app->request->get('id', null);
        $model = $this->findFormModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->referrer();
        }

        return $this->render($this->action->id, [
            'model' => $model,
        ]);
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
        if (empty($id) || empty(($model = AttributeForm::findOne(['id' => $id, 'merchant_id' => $this->getMerchantId()])))) {
            $model = new AttributeForm();
            return $model->loadDefaultValues();
        }

        return $model;
    }
}
