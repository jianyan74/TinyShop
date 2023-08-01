<?php

namespace addons\TinyShop\merchant\modules\common\controllers;

use Yii;
use common\helpers\ArrayHelper;
use common\helpers\DateHelper;
use common\enums\StatusEnum;
use common\traits\MerchantCurd;
use addons\TinyShop\common\models\common\LocalConfig;
use addons\TinyShop\merchant\controllers\BaseController;

/**
 * Class LocalConfigController
 * @package addons\TinyShop\merchant\modules\common\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class LocalConfigController extends BaseController
{
    use MerchantCurd;

    /**
     * @var LocalConfig
     */
    public $modelClass = LocalConfig::class;

    /**
     * 编辑/创建
     *
     * @return mixed
     */
    public function actionEdit()
    {
        $model = $this->findModel($this->getMerchantId());
        $model->is_start = StatusEnum::ENABLED;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->message('保存成功', $this->redirect(['edit']));
        }

        $section = DateHelper::formatHours(ArrayHelper::numBetween(0, 86400, true, 1800));

        return $this->render($this->action->id, [
            'model' => $model,
            'section' => $section,
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
        if (empty(($model = $this->modelClass::findOne(['merchant_id' => $id, 'is_start' => StatusEnum::ENABLED])))) {
            $model = new $this->modelClass;
            return $model->loadDefaultValues();
        }

        return $model;
    }
}
