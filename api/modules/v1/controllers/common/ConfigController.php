<?php

namespace addons\TinyShop\api\modules\v1\controllers\common;

use Yii;
use yii\data\ActiveDataProvider;
use api\controllers\OnAuthController;

/**
 * 公用配置
 *
 * Class ConfigController
 * @package addons\TinyShop\api\modules\v1\controllers\common
 * @author jianyan74 <751393839@qq.com>
 */
class ConfigController extends OnAuthController
{
    public $modelClass = '';

    /**
     * 不用进行登录验证的方法
     *
     * 例如： ['index', 'update', 'create', 'view', 'delete']
     * 默认全部需要验证
     *
     * @var array
     */
    protected $authOptional = ['index'];

    /**
     * @return array|ActiveDataProvider
     */
    public function actionIndex()
    {
        $merchant_id = Yii::$app->services->merchant->getAutoId();
        $config = Yii::$app->services->addonsConfig->findConfigByCache('TinyShop', $merchant_id, true);
        foreach ($config as &$item) {
            is_numeric($item) && $item = (int)$item;
        }

        return [
            'config' => $config,
        ];
    }

    /**
     * 获取基本配置
     *
     * @param int $merchant_id
     * @return string[]
     */
    public function actionBase(int $merchant_id)
    {
        $config = Yii::$app->services->config->merchantConfigAll(false, $merchant_id);

        return [
            'wechat_mp_app_id' => $config['wechat_mp_app_id'] ?? '',
        ];
    }
}
