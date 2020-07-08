<?php

namespace addons\TinyShop\services\common;

use common\enums\AppEnum;
use addons\TinyShop\common\models\base\NotifySubscriptionConfig;
use common\components\Service;
use common\enums\StatusEnum;

/**
 * Class NotifySubscriptionConfigService
 * @package addons\TinyShop\services\common
 * @author jianyan74 <751393839@qq.com>
 */
class NotifySubscriptionConfigService extends Service
{
    /**
     * @param $app_id
     * @param $member_id
     * @return NotifySubscriptionConfig|array|\yii\db\ActiveRecord
     */
    public function findByMemberId($member_id, $merchant_id, $app_id = AppEnum::API)
    {
        $config = NotifySubscriptionConfig::find()
            ->where(['member_id' => $member_id])
            ->one();

        if (!$config) {
            $config = new NotifySubscriptionConfig();
            $config->app_id = $app_id;
            $config->merchant_id = $merchant_id;
            $config->member_id = $member_id;
            $config->action = $this->getAction($app_id);
            $config->save();
        }

        return $config;
    }

    /**
     * @param $app_id
     * @return array
     */
    protected function getAction($app_id)
    {
        switch ($app_id) {
            default :
                return [
                    'all' => StatusEnum::ENABLED,
                ];
                break;
        }
    }
}