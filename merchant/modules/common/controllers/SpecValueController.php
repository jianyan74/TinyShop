<?php

namespace addons\TinyShop\merchant\modules\common\controllers;

use Yii;
use common\enums\StatusEnum;
use common\helpers\ResultHelper;
use addons\TinyShop\common\models\common\SpecValue;
use addons\TinyShop\merchant\controllers\BaseController;

/**
 * Class SpecValueController
 * @package addons\TinyShop\merchant\modules\common\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class SpecValueController extends BaseController
{
    /**
     * 创建
     *
     * @param $title
     * @param $type
     * @return array|mixed
     */
    public function actionCreate($title, $spec_id)
    {
        $model = new SpecValue();
        $model = $model->loadDefaultValues();
        $model->merchant_id = Yii::$app->services->merchant->getNotNullId();
        $model->title = $title;
        $model->spec_id = $spec_id;
        $model->is_tmp = StatusEnum::ENABLED;
        $model->save();

        return ResultHelper::json(200, 'ok', $model);
    }
}
