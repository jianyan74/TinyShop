<?php

namespace addons\TinyShop\services\common;

use yii\web\UnprocessableEntityHttpException;
use common\helpers\ArrayHelper;
use addons\TinyShop\common\models\common\LocalConfig;

/**
 * Class LocalConfigService
 * @package addons\TinyShop\services\common
 * @author jianyan74 <751393839@qq.com>
 */
class LocalConfigService
{
    /**
     * 获取配送费
     *
     * @param $merchant_id
     * @return int
     */
    public function getShippingFeeByMerchantId($merchant_id, $order_money = 0)
    {
        if (empty($model = $this->findByMerchantId($merchant_id)) || empty($model->shipping_fee)) {
            return 0;
        }

        if ($order_money < $model->order_money) {
            throw new UnprocessableEntityHttpException('最低配送金额为：' . $model->order_money);
        }

        // 运费
        $model->shipping_fee = ArrayHelper::arraySort($model->shipping_fee, 'order_money', 'desc');
        foreach ($model->shipping_fee as $item) {
            if ($order_money > $item['order_money']) {
                return $item['freight'];
            }
        }

        return $model->freight;
    }

    /**
     * @param $merchant_id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findByMerchantId($merchant_id = 0)
    {
        return LocalConfig::find()
            ->where(['merchant_id' => $merchant_id])
            ->one();
    }
}
