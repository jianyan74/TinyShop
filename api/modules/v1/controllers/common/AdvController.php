<?php

namespace addons\TinyShop\api\modules\v1\controllers\common;

use Yii;
use common\helpers\ArrayHelper;
use api\controllers\OnAuthController;
use addons\TinyShop\common\models\common\Adv;
use addons\TinyShop\common\enums\AdvLocalEnum;

/**
 * Class AdvController
 * @package addons\TinyShop\api\modules\v1\controllers\common
 * @author jianyan74 <751393839@qq.com>
 */
class AdvController extends OnAuthController
{
    /**
     * @var Adv
     */
    public $modelClass = Adv::class;

    /**
     * 不用进行登录验证的方法
     * 例如： ['index', 'update', 'create', 'view', 'delete']
     * 默认全部需要验证
     *
     * @var array
     */
    protected $authOptional = ['index'];

    /**
     * @return array|\yii\data\ActiveDataProvider
     */
    public function actionIndex()
    {
        $location = Yii::$app->request->get('location');
        $location = explode(',', $location);
        $location = array_intersect($location, AdvLocalEnum::getKeys());

        return Yii::$app->tinyShopService->adv->getListByLocals($location);
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
        if (in_array($action, ['view', 'delete', 'create', 'update'])) {
            throw new \yii\web\BadRequestHttpException('权限不足');
        }
    }
}