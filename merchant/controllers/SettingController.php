<?php

namespace addons\TinyShop\merchant\controllers;

use common\enums\AppEnum;
use Yii;
use common\helpers\AddonHelper;
use common\helpers\ArrayHelper;
use common\interfaces\AddonsSetting;
use addons\TinyShop\common\models\SettingForm;

/**
 * 参数设置
 *
 * Class SettingController
 * @package addons\TinyShop\merchant\controllers
 */
class SettingController extends BaseController implements AddonsSetting
{
    /**
     * @return mixed|string
     */
    public function actionDisplay()
    {
        $request = Yii::$app->request;
        $model = new SettingForm();
        $model->attributes = AddonHelper::getBackendConfig(true);
        if ($model->load($request->post()) && $model->validate()) {
            AddonHelper::setConfig(ArrayHelper::toArray($model), '', AppEnum::BACKEND);

            return $this->message('修改成功', $this->redirect(['display']));
        }

        return $this->render('@addons/TinyShop/backend/views/setting/display', [
            'model' => $model,
        ]);
    }
}