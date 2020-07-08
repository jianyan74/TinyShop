<?php

namespace addons\TinyShop\merchant\forms;

use addons\TinyShop\common\models\base\Spec;
use addons\TinyShop\common\models\base\SpecValue;

/**
 * Class SpecForm
 * @package addons\TinyShop\merchant\forms
 * @author jianyan74 <751393839@qq.com>
 */
class SpecForm extends Spec
{
    /**
     * @param bool $insert
     * @param array $changedAttributes
     * @throws \yii\db\Exception
     */
    public function afterSave($insert, $changedAttributes)
    {
        SpecValue::updateData($this->valueData, $this->value, $this->id, $this->merchant_id);

        parent::afterSave($insert, $changedAttributes);
    }

    public function afterDelete()
    {
        SpecValue::deleteAll(['merchant_id' => $this->merchant_id, 'spec_id' => $this->id]);
        parent::afterDelete();
    }
}