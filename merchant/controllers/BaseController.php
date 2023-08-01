<?php

namespace addons\TinyShop\merchant\controllers;

use Yii;
use common\helpers\AddonHelper;
use common\controllers\AddonsController;

/**
 * 默认控制器
 *
 * Class DefaultController
 * @package addons\TinyShop\merchant\controllers
 */
class BaseController extends AddonsController
{
    /**
     * @var string
     */
    public $layout = "@backend/views/layouts/main";

    public function init()
    {
        // 注册资源
        AddonHelper::filePath();

        parent::init();
    }
}
