<?php

namespace addons\TinyShop\merchant\modules\common\controllers;

use Yii;
use common\traits\MerchantCurd;
use addons\TinyShop\common\models\common\LocalArea;
use addons\TinyShop\merchant\controllers\BaseController;

/**
 * Class LocalAreaController
 * @package addons\TinyShop\merchant\modules\common\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class LocalAreaController extends BaseController
{
    use MerchantCurd;

    /**
     * @var LocalArea
     */
    public $modelClass = LocalArea::class;

    /**
     * 编辑/创建
     *
     * @return mixed
     */
    public function actionEdit()
    {
        $model = $this->findModel($this->getMerchantId());
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->message('保存成功', $this->redirect(['edit']));
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
