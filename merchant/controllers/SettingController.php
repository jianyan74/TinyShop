<?php

namespace addons\TinyShop\merchant\controllers;

use Yii;
use common\helpers\ArrayHelper;
use addons\TinyShop\common\forms\SettingForm;

/**
 * 参数设置
 *
 * Class SettingController
 * @package addons\TinyShop\merchant\controllers
 */
class SettingController extends BaseController
{
    /**
     * @return mixed|string
     */
    public function actionDisplay()
    {
        $request = Yii::$app->request;
        $model = new SettingForm();
        $model->attributes = Yii::$app->services->addonsConfig->getConfig();
        if ($model->load($request->post()) && $model->validate()) {
            Yii::$app->services->addonsConfig->setConfig(ArrayHelper::toArray($model));
            return $this->message('修改成功', $this->redirect(['display']));
        }

        return $this->render('display',[
            'model' => $model,
        ]);
    }
}
