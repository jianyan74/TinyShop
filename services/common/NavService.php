<?php

namespace addons\TinyShop\services\common;

use addons\TinyShop\common\models\common\Nav;

/**
 * Class NavService
 * @package addons\TinyShop\services\common
 */
class NavService
{
    /**
     * @param $merchant_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findAll($merchant_id)
    {
        return Nav::find()
            ->where(['merchant_id' => $merchant_id])
            ->asArray()
            ->all();
    }

    /**
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findOne($merchant_id, $name)
    {
        return Nav::find()
            ->where(['merchant_id' => $merchant_id, 'name' => $name])
            ->asArray()
            ->one();
    }

    /**
     * @return Nav
     */
    public function one($merchant_id, $name)
    {
        /* @var $model Nav */
        if (empty(($model = Nav::find()->where(['merchant_id' => $merchant_id, 'name' => $name])->one()))) {
            $model = new Nav();

            return $model->loadDefaultValues();
        }

        return $model;
    }
}