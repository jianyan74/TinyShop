<?php

namespace addons\TinyShop\api\modules\v1\controllers\common;

use Yii;
use api\controllers\OnAuthController;
use common\helpers\ArrayHelper;
use addons\TinyShop\common\models\common\Helper;

/**
 * Class HelperController
 * @package addons\TinyShop\api\modules\v1\controllers\common
 * @author jianyan74 <751393839@qq.com>
 */
class HelperController extends OnAuthController
{
    /**
     * @var Helper
     */
    public $modelClass = Helper::class;


    /**
     * 不用进行登录验证的方法
     * 例如： ['index', 'update', 'create', 'view', 'delete']
     * 默认全部需要验证
     *
     * @var array
     */
    protected $authOptional = ['index', 'view'];

    /**
     * @return array|\yii\data\ActiveDataProvider
     */
    public function actionIndex()
    {
        $data = Yii::$app->tinyShopService->helper->findAll();

        return ArrayHelper::itemsMerge($data, 0, 'id', 'pid', 'child');
    }

    /**
     * 权限验证
     *
     * @param string $action 当前的方法
     * @param null $model 当前的模型类
     * @param array $params $_GET变量
     * @throws \yii\web\BadRequestHttpException
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        // 方法名称
        if (in_array($action, ['delete', 'create', 'update'])) {
            throw new \yii\web\BadRequestHttpException('权限不足');
        }
    }
}