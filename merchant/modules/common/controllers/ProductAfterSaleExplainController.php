<?php

namespace addons\TinyShop\merchant\modules\common\controllers;

use addons\TinyShop\merchant\modules\common\forms\ProductAfterSaleExplainForm;

/**
 * Class ProductAfterSaleExplainController
 * @package addons\TinyShop\merchant\modules\common\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class ProductAfterSaleExplainController extends BaseSettingController
{
    /**
     * @var ProductAfterSaleExplainForm
     */
    public $modelClass = ProductAfterSaleExplainForm::class;
}
