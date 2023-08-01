<?php

namespace addons\TinyShop\services\marketing;

use common\enums\StatusEnum;
use addons\TinyShop\common\models\marketing\RechargeConfig;

/**
 * Class RechargeConfigService
 * @package addons\TinyShop\services\marketing
 * @author jianyan74 <751393839@qq.com>
 */
class RechargeConfigService
{
    /**
     * @param $money
     * @return array|\yii\db\ActiveRecord|null|RechargeConfig
     */
    public function getGiveMoney($money)
    {
        return RechargeConfig::find()
            ->where(['<=', 'price', $money])
            ->andWhere(['status' => StatusEnum::ENABLED])
            ->orderBy('price desc')
            ->one();
    }
}
