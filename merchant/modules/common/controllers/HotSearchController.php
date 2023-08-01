<?php

namespace addons\TinyShop\merchant\modules\common\controllers;

use addons\TinyShop\merchant\modules\common\forms\HotSearchForm;

/**
 * Class HotSearchController
 * @package addons\TinyShop\merchant\modules\common\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class HotSearchController extends BaseSettingController
{
    /**
     * @var HotSearchForm
     */
    public $modelClass = HotSearchForm::class;
}
