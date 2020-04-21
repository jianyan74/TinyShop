<?php

namespace addons\TinyShop\services\marketing;

use Yii;
use common\components\Service;
use common\enums\StatusEnum;
use common\helpers\StringHelper;
use addons\TinyShop\common\models\marketing\FullMail;

/**
 * Class FullMailService
 * @package addons\TinyShop\services\marketing
 * @author jianyan74 <751393839@qq.com>
 */
class FullMailService extends Service
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
    public function one()
    {
        /* @var $model FullMail */
        if (empty(($model = FullMail::find()->where(['merchant_id' => Yii::$app->services->merchant->getId()])->one()))) {
            $model = new FullMail();

            return $model->loadDefaultValues();
        }

        return $model;
    }

    /**
     * 根据地址计算满额包邮
     *
     * @param $product_money
     * @param $address
     * @return FullMail|bool
     */
    public function postage($product_money, $address)
    {
        if (empty($product_money) || empty($address)) {
            return false;
        }

        $fullMail = $this->one();
        if (
            $fullMail['is_open'] == StatusEnum::ENABLED &&
            $product_money >= $fullMail['full_mail_money'] &&
            in_array($address['city_id'], StringHelper::parseAttr($fullMail['no_mail_city_ids']))
        ) {
            return $fullMail;
        }

        return false;
    }
}