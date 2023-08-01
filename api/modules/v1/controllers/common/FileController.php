<?php

namespace addons\TinyShop\api\modules\v1\controllers\common;

use Yii;
use common\traits\FileAction;
use api\controllers\OnAuthController;

/**
 * 文件上传
 *
 * Class FileController
 * @package addons\TinyShop\api\modules\v1\controllers\common
 * @author jianyan74 <751393839@qq.com>
 */
class FileController extends OnAuthController
{
    use FileAction;

    /**
     * @var string
     */
    public $modelClass = '';

    public function beforeAction($action)
    {
        // 不记录上传的图片
        Yii::$app->params['fileWriteTable'] = false;

        return parent::beforeAction($action);
    }
}
