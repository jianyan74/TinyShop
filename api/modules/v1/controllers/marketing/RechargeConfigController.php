<?php

namespace addons\TinyShop\api\modules\v1\controllers\marketing;

use api\controllers\OnAuthController;
use common\enums\StatusEnum;
use addons\TinyShop\common\models\marketing\RechargeConfig;

/**
 * 充值配置
 *
 * Class RechargeConfigController
 * @package addons\TinyShop\api\modules\v1\controllers\marketing
 * @author jianyan74 <751393839@qq.com>
 */
class RechargeConfigController extends OnAuthController
{
    protected $authOptional = ['index'];

    /**
     * @var RechargeConfig
     */
    public $modelClass = RechargeConfig::class;

    /**
     * @return array
     */
    public function actionIndex()
    {
        return $this->modelClass::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->orderBy('price asc')
            ->asArray()
            ->all();
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
        if (in_array($action, ['delete', 'update', 'create'])) {
            throw new \yii\web\BadRequestHttpException('权限不足');
        }
    }
}
