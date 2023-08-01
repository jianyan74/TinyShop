<?php

namespace addons\TinyShop\api\modules\v1\controllers\common;

use Yii;
use api\controllers\OnAuthController;
use common\models\common\Provinces;

/**
 * 省市区
 *
 * Class ProvincesController
 * @package addons\TinyShop\api\modules\v1\controllers\common
 * @author jianyan74 <751393839@qq.com>
 */
class ProvincesController extends OnAuthController
{
    /**
     * @var Provinces
     */
    public $modelClass = Provinces::class;

    /**
     * 不用进行登录验证的方法
     * 例如： ['index', 'update', 'create', 'view', 'delete']
     * 默认全部需要验证
     *
     * @var array
     */
    protected $authOptional = ['index'];

    /**
     * 获取省市区
     *
     * @param int $pid
     * @return array|yii\data\ActiveDataProvider
     */
    public function actionIndex()
    {
        $pid = Yii::$app->request->get('pid', 0);

        return Yii::$app->services->provinces->getCityByPid($pid);
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
        if (in_array($action, ['create', 'update', 'delete'])) {
            throw new \yii\web\BadRequestHttpException('权限不足');
        }
    }
}
