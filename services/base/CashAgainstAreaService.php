<?php

namespace addons\TinyShop\services\base;

use common\components\Service;
use addons\TinyShop\common\models\base\CashAgainstArea;

/**
 * Class CashAgainstAreaService
 * @package addons\TinyShop\services\base
 * @author jianyan74 <751393839@qq.com>
 */
class CashAgainstAreaService extends Service
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