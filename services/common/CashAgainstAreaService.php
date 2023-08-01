<?php

namespace addons\TinyShop\services\common;

use addons\TinyShop\common\models\common\CashAgainstArea;

/**
 * Class CashAgainstAreaService
 * @package addons\TinyShop\services\common
 * @author jianyan74 <751393839@qq.com>
 */
class CashAgainstAreaService
{
    /**
     * @param $merchant_id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findOne($merchant_id)
    {
        return CashAgainstArea::find()
            ->where(['merchant_id' => $merchant_id])
            ->asArray()
            ->one();
    }
}
