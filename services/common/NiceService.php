<?php

namespace addons\TinyShop\services\common;

use common\components\Service;
use addons\TinyShop\common\models\common\Nice;

/**
 * Class NiceService
 * @package addons\TinyShop\services\common
 * @author jianyan74 <751393839@qq.com>
 */
class NiceService extends Service
{

    /**
     * @param $topic_id
     * @param $topic_type
     * @param $member_id
     * @return Nice|array|\yii\db\ActiveRecord|null
     */
    public function findByTopicId($topic_id, $topic_type, $member_id)
    {
        $model = Nice::find()
            ->where([
                'topic_id' => $topic_id,
                'topic_type' => $topic_type,
                'member_id' => $member_id,
            ])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->one();

        if (!$model) {
            $model = new Nice();
        }

        return $model;
    }

    /**
     * @param $id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findById($id, $member_id)
    {
        return Nice::find()
            ->where(['id' => $id, 'member_id' => $member_id])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->one();
    }
}