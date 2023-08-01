<?php

namespace addons\TinyShop\merchant\modules\common\controllers;

use Yii;
use addons\TinyShop\merchant\modules\common\forms\MaintenanceForm;

/**
 * Class MaintenanceController
 * @package addons\TinyShop\merchant\modules\common\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class MaintenanceController extends BaseSettingController
{
    /**
     * @var MaintenanceForm
     */
    public $modelClass = MaintenanceForm::class;
}
