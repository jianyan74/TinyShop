<?php

namespace addons\TinyShop\merchant\modules\base\controllers;

use Yii;
use common\traits\MerchantCurd;
use addons\TinyShop\common\models\base\LocalDistributionArea;
use addons\TinyShop\merchant\controllers\BaseController;

/**
 * Class LocalDistributionAreaController
 * @package addons\TinyShop\merchant\modules\base\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class LocalDistributionAreaController extends BaseController
{
    use MerchantCurd;

    /**
     * @var LocalDistributionArea
     */
    public $modelClass = LocalDistributionArea::class;

    /**
     * 编辑/创建
     *
     * @return mixed
     */
    public function actionEdit()
    {
        $model = $this->findModel($this->getMerchantId());
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['edit']);
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