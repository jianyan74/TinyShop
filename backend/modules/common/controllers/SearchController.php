<?php

namespace addons\TinyShop\backend\modules\common\controllers;

use Yii;
use addons\TinyShop\merchant\forms\HotSearchForm;
use common\helpers\ArrayHelper;
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
        if ($model->load($request->post()) && $model->validate()) {
            $this->setConfig(ArrayHelper::toArray($model));

            return $this->message('修改成功', $this->redirect(['index']));
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }
}