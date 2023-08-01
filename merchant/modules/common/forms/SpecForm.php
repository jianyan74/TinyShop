<?php

namespace addons\TinyShop\merchant\modules\common\forms;

use Yii;
use common\helpers\ArrayHelper;
use addons\TinyShop\common\models\common\Spec;

/**
 * Class SpecForm
 * @package addons\TinyShop\merchant\modules\common\forms
 */
class SpecForm extends Spec
{
    public $valueData;

    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['valueData', 'safe']
        ]);
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     * @throws \yii\db\Exception
     */
    public function afterSave($insert, $changedAttributes)
    {
        Yii::$app->tinyShopService->specValue->updateData($this->valueData, $this->value, $this->id, $this->merchant_id);

        parent::afterSave($insert, $changedAttributes);
    }
}