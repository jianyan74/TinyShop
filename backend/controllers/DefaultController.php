<?php

namespace addons\TinyShop\backend\controllers;

use Yii;

/**
 * 默认控制器
 *
 * Class DefaultController
 * @package addons\TinyShop\backend\controllers
 */
class DefaultController extends BaseController
{
    /**
    * 首页
    *
    * @return string
    */
    public function actionIndex()
    {
        return $this->render('index',[

        ]);
    }
}