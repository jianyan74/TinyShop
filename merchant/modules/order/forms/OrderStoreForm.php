<?php

namespace addons\TinyShop\merchant\modules\order\forms;

use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use common\helpers\RegularHelper;
use addons\TinyShop\common\models\order\Store;

/**
 * Class OrderStoreForm
 * @package addons\TinyShop\merchant\modules\order\forms
 * @author jianyan74 <751393839@qq.com>
 */
class OrderStoreForm extends Store
{
    /**
     * @return array
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['buyer_name', 'buyer_mobile'], 'required'],
            ['buyer_mobile', 'match', 'pattern' => RegularHelper::mobile(), 'message' => '不是一个有效的手机号码'],
            ['buyer_mobile', 'verifyStatus'],
        ]);
    }

    /**
     * @param $attribute
     */
    public function verifyStatus($attribute)
    {
        if ($this->pickup_status == StatusEnum::ENABLED) {
            $this->addError($attribute, '已经提货成功，请刷新查看');
        }
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        $this->pickup_status = StatusEnum::ENABLED;
        $this->pickup_time = time();

        return parent::beforeSave($insert);
    }
}
