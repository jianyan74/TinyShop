<?php

namespace addons\TinyShop\services\common;

use addons\TinyShop\common\models\common\LocalArea;

/**
 * Class LocalAreaService
 * @package addons\TinyShop\services\common
 * @author jianyan74 <751393839@qq.com>
 */
class LocalAreaService
{
    /**
     * @param $merchant_id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findOne($merchant_id)
    {
        return LocalArea::find()
            ->where(['merchant_id' => $merchant_id])
            ->asArray()
            ->one();
    }
}
