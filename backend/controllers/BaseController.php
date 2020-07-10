<?php

namespace addons\TinyShop\backend\controllers;

use common\helpers\AddonHelper;
use Yii;
use common\controllers\AddonsController;

/**
 * 默认控制器
 *
 * Class DefaultController
 * @package addons\TinyShop\backend\controllers
 */
class BaseController extends AddonsController
{
    /**
     * @var string
     */
    public $layout = "@backend/views/layouts/main";

    public function init()
    {
        AddonHelper::filePath();

        parent::init();
    }
}