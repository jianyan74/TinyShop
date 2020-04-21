<?php

namespace addons\TinyShop\merchant\modules\base\controllers;

use Yii;
use common\helpers\ArrayHelper;
use common\helpers\DateHelper;
use common\enums\StatusEnum;
use common\traits\MerchantCurd;
use addons\TinyShop\merchant\forms\LocalDistributionConfigForm;
use addons\TinyShop\merchant\controllers\BaseController;

/**
 * Class LocalDistributionAreaController
 * @package addons\TinyShop\merchant\modules\base\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class LocalDistributionConfigController extends BaseController
{
    use MerchantCurd;

    /**
     * @var LocalDistributionConfigForm
     */
    public $modelClass = LocalDistributionConfigForm::class;

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

        $forenoon_start = DateHelper::formatHours(ArrayHelper::numBetween(0, 43200, true, 1800));
        $forenoon_end = DateHelper::formatHours(ArrayHelper::numBetween(0, 43200, true, 1800));
        $afternoon_start = DateHelper::formatHours(ArrayHelper::numBetween(43200, 86400, true, 1800));
        $afternoon_end = DateHelper::formatHours(ArrayHelper::numBetween(43200, 86400, true, 1800));

        return $this->render($this->action->id, [
            'model' => $model,
            'forenoon_start' => $forenoon_start,
            'forenoon_end' => $forenoon_end,
            'afternoon_start' => $afternoon_start,
            'afternoon_end' => $afternoon_end,
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