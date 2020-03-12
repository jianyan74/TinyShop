<?php

namespace addons\TinyShop\services\base;

use Yii;
use common\components\Service;
use addons\TinyShop\common\models\base\SpecValue;

/**
 * Class SpecValue
 * @package addons\TinyShop\common\services\base
 * @author jianyan74 <751393839@qq.com>
 */
class SpecValueService extends Service
{
    /**
     * @param array $ids
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findByIds(array $ids)
    {
        return SpecValue::find()
            ->andWhere(['in', 'id', $ids])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->asArray()
            ->all();
    }
}