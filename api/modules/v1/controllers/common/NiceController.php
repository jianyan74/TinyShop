<?php

namespace addons\TinyShop\api\modules\v1\controllers\common;

use Yii;
use yii\base\Model;
use addons\TinyShop\common\models\common\Nice;

/**
 * 点赞
 *
 * Class NiceController
 * @package addons\TinyShop\api\modules\v1\controllers\common
 * @author jianyan74 <751393839@qq.com>
 */
class NiceController extends FollowController
{
    /**
     * @var Nice
     */
    public $modelClass = Nice::class;

    /**
     * @param $topic_id
     * @param $topic_type
     * @return Nice|array|\yii\db\ActiveRecord|null
     */
    public function findByTopicId($topic_id, $topic_type)
    {
        return Yii::$app->tinyShopService->nice->findByTopicId($topic_id, $topic_type, Yii::$app->user->identity->member_id);
    }

    /**
     * @param Nice
     * @return Nice|array|\yii\db\ActiveRecord|null
     */
    public function findById($id)
    {
        return Yii::$app->tinyShopService->nice->findById($id, Yii::$app->user->identity->member_id);
    }

    /**
     * @param Nice $model
     * @param $class
     * @param $num
     * @return mixed
     */
    public function callBack($model, $class, $num)
    {
        /** @var Model $class */
        return $class::updateAllCounters(['nice_num' => $num], ['id' => $model->topic_id]);
    }
}