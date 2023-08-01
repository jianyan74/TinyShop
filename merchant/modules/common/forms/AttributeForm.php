<?php

namespace addons\TinyShop\merchant\modules\common\forms;

use Yii;
use common\helpers\ArrayHelper;
use addons\TinyShop\common\models\common\Attribute;

/**
 * Class AttributeForm
 * @package addons\TinyShop\merchant\modules\common\forms
 * @author jianyan74 <751393839@qq.com>
 */
class AttributeForm extends Attribute
{
    public $valueData;

    /**
     * @return array
     */
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
        Yii::$app->tinyShopService->attributeValue->updateData($this->valueData, $this->value, $this->id, $this->merchant_id);

        parent::afterSave($insert, $changedAttributes);
    }
}
