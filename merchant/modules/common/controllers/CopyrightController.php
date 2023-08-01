<?php

namespace addons\TinyShop\merchant\modules\common\controllers;

use addons\TinyShop\merchant\modules\common\forms\CopyrightForm;

/**
 * Class CopyrightController
 * @package addons\TinyShop\merchant\modules\common\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class CopyrightController extends BaseSettingController
{
    /**
     * @var CopyrightForm
     */
    public $modelClass = CopyrightForm::class;
}
