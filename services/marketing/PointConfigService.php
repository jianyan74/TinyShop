<?php

namespace addons\TinyShop\services\marketing;

use Yii;
use common\helpers\BcHelper;
use common\enums\StatusEnum;
use common\components\Service;
use addons\TinyShop\common\models\marketing\PointConfig;
use addons\TinyShop\common\enums\PointConfigDeductionTypeEnum;

/**
 * Class PointConfigService
 * @package addons\TinyShop\services\marketing
 * @author jianyan74 <751393839@qq.com>
 */
class PointConfigService extends Service
{
    /**
     * @param $config
     * @param $order_money
     * @param $user_point
     * @return array|false|void
     */
    public function getMaxConfig($orderMoney = 0, $userPoint = 0, $isBuy = false)
    {
        $config = $this->findOne(0);
        if (
            empty($config) ||
            $config['status'] != StatusEnum::ENABLED ||
            $orderMoney == 0 ||
            $userPoint == 0
        ) {
            return false;
        }

        // 支付情况下不满足
        if ($isBuy == true && $orderMoney < $config['min_order_money']) {
            return false;
        }

        // 最多可抵
        $maxMoney = BcHelper::mul($config['convert_rate'], $userPoint);
        switch ($config['deduction_type']) {
            // 总上限
            case PointConfigDeductionTypeEnum::MONEY :
                $maxMoney > $config['max_deduction_money'] && $maxMoney = $config['max_deduction_money'];
                // 下单判断总上限
                if ($isBuy == true) {
                    $maxMoney > $orderMoney && $maxMoney = $orderMoney;
                }
                break;
            // 比率
            case PointConfigDeductionTypeEnum::RATE :
                // 下单判断总上限
                if ($isBuy == true) {
                    $tmpMaxMoney = BcHelper::mul($orderMoney, $config['max_deduction_rate']);
                    $tmpMaxMoney < $maxMoney && $maxMoney = $tmpMaxMoney;
                }
                break;
        }

        // 比率过小无积分不使用抵扣
        $maxPoint = (int)BcHelper::div($maxMoney, $config['convert_rate']);
        if ($maxPoint == 0) {
            return false;
        }

        return [
            'maxMoney' => floatval($maxMoney),
            'maxPoint' => $maxPoint,
            'minOrderMoney' => floatval($config['min_order_money']),
            'convertRate' => floatval($config['convert_rate']),
        ];
    }

    /**
     * @return array|\yii\db\ActiveRecord|null|PointConfig
     */
    public function findOne($merchant_id)
    {
        $merchant_id = 0;

        return PointConfig::find()
            ->where(['merchant_id' => $merchant_id])
            ->asArray()
            ->one();
    }

    /**
     * @return PointConfig
     */
    public function one($merchant_id)
    {
        /* @var $model PointConfig */
        if (empty(($model = PointConfig::find()->where(['merchant_id' => $merchant_id])->one()))) {
            $model = new PointConfig();

            return $model->loadDefaultValues();
        }

        return $model;
    }
}
