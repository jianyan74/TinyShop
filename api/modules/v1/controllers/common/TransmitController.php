<?php

namespace addons\TinyShop\api\modules\v1\controllers\common;

use Yii;
use yii\base\Model;
use addons\TinyShop\common\models\common\Transmit;

/**
 * 转发
 *
 * Class TransmitController
 * @package addons\TinyShop\api\modules\v1\controllers\common
 * @author jianyan74 <751393839@qq.com>
 */
class TransmitController extends FollowController
{
    /**
     * @var Transmit
     */
    public $modelClass = Transmit::class;

    /**
     * @param $topic_id
     * @param $topic_type
     * @return Transmit|array|\yii\db\ActiveRecord|null
     */
    public function findByTopicId($topic_id, $topic_type)
    {
        return Yii::$app->tinyShopService->transmit->findByTopicId($topic_id, $topic_type, Yii::$app->user->identity->member_id);
    }

    /**
     * @param $id
     * @return Transmit|array|\yii\db\ActiveRecord|null
     */
    public function findById($id)
    {
        return Yii::$app->tinyShopService->transmit->findById($id, Yii::$app->user->identity->member_id);
    }

    /**
     * @param Transmit $model
     * @param $class
     * @param $num
     * @return mixed
     */
    public function callBack($model, $class, $num)
    {
        /** @var Model $class */
        return $class::updateAllCounters(['transmit_num' => $num], ['id' => $model->topic_id]);
    }
}