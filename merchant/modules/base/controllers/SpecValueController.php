<?php

namespace addons\TinyShop\merchant\modules\base\controllers;

use Yii;
use common\helpers\ResultHelper;
use addons\TinyShop\common\models\base\SpecValue;
use addons\TinyShop\merchant\controllers\BaseController;

/**
 * Class SpecValueController
 * @package addons\TinyShop\merchant\modules\base\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class SpecValueController extends BaseController
{
    /**
     * @return array|mixed
     */
    public function actionCreate()
    {
        $spec_id = Yii::$app->request->post('spec_id');
        $value = Yii::$app->request->post('value');
        if (!$spec_id || !($spec = Yii::$app->tinyShopService->baseSpec->findById($spec_id))) {
            return ResultHelper::json(422, '找不到规格');
        }

        $model = new SpecValue();
        $model = $model->loadDefaultValues();
        $model->spec_id = $spec_id;
        $model->title = $value;
        if (!$model->save()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        return ResultHelper::json(200, '添加成功', $model);
    }
}