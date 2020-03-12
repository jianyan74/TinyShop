<?php

namespace addons\TinyShop\api\modules\v1\controllers\member;

use api\controllers\OnAuthController;
use common\enums\StatusEnum;
use common\models\member\RechargeConfig;

/**
 * 充值配置
 *
 * Class RechargeConfigController
 * @package addons\TinyShop\api\modules\v1\controllers\member
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
}