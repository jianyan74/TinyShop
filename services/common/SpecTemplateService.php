<?php

namespace addons\TinyShop\services\common;

use Yii;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use addons\TinyShop\common\models\common\SpecTemplate;

/**
 * Class SpecTemplateService
 * @package addons\TinyShop\services\common
 * @author jianyan74 <751393839@qq.com>
 */
class SpecTemplateService
{
    /**
     * @return array
     */
    public function getMap()
    {
        return ArrayHelper::map($this->findAll(), 'id', 'title');
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findAll()
    {
        return SpecTemplate::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere(['merchant_id' => Yii::$app->services->merchant->getNotNullId()])
            ->orderBy('sort asc, id desc')
            ->asArray()
            ->all();
    }
}
