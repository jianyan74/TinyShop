<?php

namespace addons\TinyShop\merchant\modules\marketing\controllers;

use Yii;
use common\helpers\ArrayHelper;
use addons\TinyShop\merchant\controllers\BaseController;
use addons\TinyShop\merchant\modules\marketing\forms\ProductPosterForm;

/**
 * 商品海报
 *
 * Class ProductPosterController
 * @package addons\TinyShop\merchant\modules\marketing\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class ProductPosterController extends BaseController
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        $merchantId = Yii::$app->services->merchant->getNotNullId();

        $model = new ProductPosterForm();
        $model->attributes = Yii::$app->services->addonsConfig->getConfig('TinyShop', $merchantId);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            Yii::$app->services->addonsConfig->setConfig(ArrayHelper::toArray($model), 'TinyShop', $merchantId);

            return $this->message('保存成功', $this->redirect(['index']));
        }

        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }
}
