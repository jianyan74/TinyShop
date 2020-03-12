<?php

namespace addons\TinyShop\api\modules\v1\controllers\common;

use Yii;
use yii\base\Model;
use addons\TinyShop\common\models\common\Collect;

/**
 * 收藏
 *
 * Class CollectController
 * @package addons\TinyShop\api\modules\v1\controllers\common
 * @author jianyan74 <751393839@qq.com>
 */
class CollectController extends FollowController
{
    /**
     * @var Collect
     */
    public $modelClass = Collect::class;

    /**
     * @param $topic_id
     * @param $topic_type
     * @return Collect|array|\yii\db\ActiveRecord|null
     */
    public function findByTopicId($topic_id, $topic_type)
    {
        return Yii::$app->tinyShopService->collect->findByTopicId($topic_id, $topic_type, Yii::$app->user->identity->member_id);
    }

    /**
     * @param $id
     * @return Collect|array|\yii\db\ActiveRecord|null
     */
    public function findById($id)
    {
        return Yii::$app->tinyShopService->collect->findById($id, Yii::$app->user->identity->member_id);
    }

    /**
     * @param Collect $model
     * @param $class
     * @param $num
     * @return mixed
     */
    public function callBack($model, $class, $num)
    {
        /** @var Model $class */
        return $class::updateAllCounters(['collect_num' => $num], ['id' => $model->topic_id]);
    }
}