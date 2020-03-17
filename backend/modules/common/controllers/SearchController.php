<?php

namespace addons\TinyShop\backend\modules\common\controllers;

use Yii;
use common\helpers\ArrayHelper;
use common\enums\AppEnum;
use common\helpers\AddonHelper;
use addons\TinyShop\merchant\forms\HotSearchForm;
use addons\TinyShop\backend\controllers\BaseController;

/**
 * 搜索
 *
 * Class SearchController
 * @package addons\TinyShop\backend\modules\common\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class SearchController extends BaseController
{
    /**
     * @return mixed|string
     */
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $model = new HotSearchForm();
        $model->attributes = $this->getConfig();
        $model->attributes = AddonHelper::getBackendConfig(true);
        if ($model->load($request->post()) && $model->validate()) {
            AddonHelper::setConfig(ArrayHelper::toArray($model), '', AppEnum::BACKEND);

            return $this->message('修改成功', $this->redirect(['index']));
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }
}