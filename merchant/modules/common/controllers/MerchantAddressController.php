<?php

namespace addons\TinyShop\merchant\modules\common\controllers;

use Yii;
use common\traits\MerchantCurd;
use common\enums\StatusEnum;
use common\models\base\SearchModel;
use addons\TinyShop\merchant\controllers\BaseController;
use addons\TinyShop\common\models\common\MerchantAddress;
use addons\TinyShop\merchant\modules\common\forms\MerchantAddressForm;

/**
 * Class MerchantAddressController
 * @package addons\TinyShop\merchant\modules\common\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class MerchantAddressController extends BaseController
{
    use MerchantCurd;

    /**
     * @var MerchantAddressForm
     */
    public $modelClass = MerchantAddressForm::class;

    /**
     * 首页
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        $searchModel = new SearchModel([
            'model' => MerchantAddress::class,
            'scenario' => 'default',
            'partialMatchAttributes' => ['contacts', 'mobile'], // 模糊查询
            'defaultOrder' => [
                'sort' => SORT_ASC,
                'id' => SORT_DESC,
            ],
            'pageSize' => $this->pageSize,
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->andWhere(['>=', 'status', StatusEnum::DISABLED])
            ->andWhere(['merchant_id' => Yii::$app->services->merchant->getNotNullId()]);

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
        $model = $this->findModel($this->getMerchantId());
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->message('保存成功', $this->redirect(['index']));
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
    protected function findModel($id)
    {
        /* @var $model \yii\db\ActiveRecord */
        if (empty(($model = $this->modelClass::findOne(['merchant_id' => $id])))) {
            $model = new $this->modelClass;
            return $model->loadDefaultValues();
        }

        return $model;
    }
}
