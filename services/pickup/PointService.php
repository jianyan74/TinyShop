<?php

namespace addons\TinyShop\services\pickup;

use common\components\Service;
use common\enums\StatusEnum;
use addons\TinyShop\common\models\pickup\Point;
use common\helpers\ArrayHelper;

/**
 * Class PointService
 * @package addons\TinyShop\services\pickup
 * @author jianyan74 <751393839@qq.com>
 */
class PointService extends Service
{
    public function getMap()
    {
        return ArrayHelper::map($this->getList(), 'id', 'name');
    }

    /**
     * @param $id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findById($id)
    {
        return Point::find()
            ->where(['id' => $id, 'status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->one();
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getList()
    {
        return Point::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->orderBy('sort asc, id desc')
            ->asArray()
            ->all();
    }
}