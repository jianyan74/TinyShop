<?php

namespace addons\TinyShop\backend\modules\common\controllers;

use Yii;
use addons\TinyShop\backend\controllers\BaseController;
use addons\TinyShop\backend\modules\common\forms\CopyrightForm;
use common\helpers\ArrayHelper;

/**
 * Class CopyrightController
 * @package addons\TinyShop\backend\modules\common\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class CopyrightController extends BaseController
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        $model = new CopyrightForm();
        $model->attributes = $this->getConfig();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $this->setConfig(ArrayHelper::toArray($model));

            return $this->message('保存成功', $this->redirect(['index']));
        }

        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }
}