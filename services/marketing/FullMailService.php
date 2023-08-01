<?php

namespace addons\TinyShop\services\marketing;

use common\enums\StatusEnum;
use common\helpers\StringHelper;
use addons\TinyShop\common\models\marketing\FullMail;

/**
 * Class FullMailService
 * @package addons\TinyShop\services\marketing
 */
class FullMailService
{
    /**
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findOne($merchant_id)
    {
        return FullMail::find()
            ->where(['merchant_id' => $merchant_id])
            ->asArray()
            ->one();
    }

    /**
     * @return FullMail
     */
    public function one($merchant_id)
    {
        /* @var $model FullMail */
        if (empty($model = FullMail::find()->where(['merchant_id' => $merchant_id])->one())) {
            $model = new FullMail();

            return $model->loadDefaultValues();
        }

        return $model;
    }

    /**
     * 根据地址计算满额包邮
     *
     * @param $money
     * @param $address
     * @param $merchant_id
     * @return FullMail|bool
     */
    public function postage($money, $address, $merchant_id)
    {
        if (empty($money) || empty($address)) {
            return false;
        }

        $fullMail = $this->one($merchant_id);
        if (
            $fullMail['status'] == StatusEnum::ENABLED &&
            $money >= $fullMail['full_mail_money'] &&
            !in_array($address['city_id'], StringHelper::parseAttr($fullMail['no_mail_city_ids']))
        ) {
            return $fullMail;
        }

        return false;
    }
}
