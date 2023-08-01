<?php

namespace addons\TinyShop\merchant\modules\common\controllers;

use Yii;
use common\helpers\ArrayHelper;
use addons\TinyShop\merchant\controllers\BaseController;

/**
 * Class BaseSettingController
 * @package addons\TinyShop\merchant\modules\common\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class BaseSettingController extends BaseController
{
    public $modelClass;

    /**
     * @return mixed|string
     */
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $model = new $this->modelClass();
        $model->attributes = Yii::$app->services->addonsConfig->getConfig();
        if ($model->load($request->post()) && $model->validate()) {
            Yii::$app->services->addonsConfig->setConfig(ArrayHelper::toArray($model));

            return $this->message('保存成功', $this->redirect(['index']));
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }
}
