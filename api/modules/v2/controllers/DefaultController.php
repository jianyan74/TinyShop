<?php

namespace addons\TinyShop\api\modules\v2\controllers;

use Yii;
use api\controllers\OnAuthController;
use api\controllers\UserAuthController;

/**
 * 默认
 *
 * Class DefaultController
 * @package addons\TinyShop\api\controllers\v2
 */
class DefaultController extends OnAuthController
{
    public $modelClass = '';

    /**
     * 不用进行登录验证的方法
     * 例如： ['index', 'update', 'create', 'view', 'delete']
     * 默认全部需要验证
     *
     * @var array
     */
    protected $authOptional = ['index'];

    /**
     * 首页
     *
     * @return string
     */
    public function actionIndex()
    {
        return 'Hello world';
    }
}