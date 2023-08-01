<?php

namespace addons\TinyShop\services\common;

use Yii;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use common\components\Service;
use addons\TinyShop\common\models\common\Supplier;

/**
 * Class SupplierService
 * @package addons\TinyShop\services\common
 */
class SupplierService extends Service
{
    /**
     * @return array
     */
    public function getMap()
    {
        return ArrayHelper::map($this->findAll(), 'id', 'name');
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findAll()
    {
        return Supplier::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere(['merchant_id' => Yii::$app->services->merchant->getNotNullId()])
            ->orderBy('sort asc, id desc')
            ->asArray()
            ->all();
    }
}
