<?php

namespace addons\TinyShop\services\common;

use Yii;
use common\enums\StatusEnum;
use addons\TinyShop\common\models\common\NotifyAnnounce;

/**
 * Class NotifyAnnounceService
 * @package addons\TinyShop\services\common
 */
class NotifyAnnounceService
{
    /**
     * 获取公告
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findByCustom()
    {
        return NotifyAnnounce::find()
            ->select(['id', 'title', 'cover', 'view', 'synopsis', 'created_at'])
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere(['merchant_id' => Yii::$app->services->merchant->getNotNullId()])
            ->orderBy('id desc')
            ->cache(30)
            ->limit(20)
            ->asArray()
            ->all();
    }

    /**
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findById($id)
    {
        return NotifyAnnounce::find()
            ->where(['id' => $id])
            ->andWhere(['status' => StatusEnum::ENABLED])
            ->one();
    }
}
