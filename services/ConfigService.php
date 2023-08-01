<?php

namespace addons\TinyShop\services;

use Yii;
use common\components\BaseAddonConfigService;
use addons\TinyShop\common\forms\SettingForm;

/**
 * Class ConfigService
 *
 * @package addons\TinyShop\services
 */
class ConfigService extends BaseAddonConfigService
{
    /**
     * @var string
     */
    public $addonName = "TinyShop";

    /**
     * @var SettingForm
     */
    public $settingForm = SettingForm::class;

    /**
     * @param int $merchant_id
     * @return SettingForm
     */
    public function setting($merchant_id = 0)
    {
        return parent::setting($merchant_id);
    }
}
