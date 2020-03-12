<?php

namespace addons\TinyShop\api\modules\v1\controllers\common;

use Yii;
use yii\base\Model;
use addons\TinyShop\common\enums\CommonTypeEnum;
use addons\TinyShop\common\models\common\Collect;
use api\controllers\OnAuthController;
use common\enums\StatusEnum;
use common\helpers\ResultHelper;

/**
 * Class FollowController
 * @package addons\TinyShop\api\modules\v1\controllers\common
 * @author jianyan74 <751393839@qq.com>
 */
abstract class FollowController extends OnAuthController
{
    /**
     * @return array|mixed|\yii\db\ActiveRecord|null
     */
    public function actionCreate()
    {
        $topic_id = Yii::$app->request->post('topic_id');
        $topic_type = Yii::$app->request->post('topic_type');

        /** @var Model $class */
        if (!($class = CommonTypeEnum::getValue($topic_type))) {
            return ResultHelper::json(422, '找不到可用的类型');
        }

        $model = $this->findByTopicId($topic_id, $topic_type);
        if ($model->status == StatusEnum::ENABLED) {
            return ResultHelper::json(422, '请不要重复操作');
        }

        // 开始事物
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model->attributes = Yii::$app->request->post();
            $model->member_id = Yii::$app->user->identity->member_id;
            $model->status = StatusEnum::ENABLED;
            if (!$model->save()) {
                // 返回数据验证失败
                return ResultHelper::json(422, $this->getError($model));
            }

            // 收藏回调
            $this->callBack($model, $class, 1);

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            // 返回数据验证失败
            return ResultHelper::json(422, $e->getMessage());
        }

        return $model;
    }

    /**
     * 取消收藏
     *
     * @param $id
     * @return bool|mixed
     * @throws \Throwable
     */
    public function actionDelete($id)
    {
        /** @var Collect $model */
        $model = $this->findById($id);
        if (!$model) {
            return ResultHelper::json(422, '找不到可用的类型');
        }

        /** @var Model $class */
        if (!($class = CommonTypeEnum::getValue($model->topic_type))) {
            return ResultHelper::json(422, '找不到可用的类型');
        }

        // 开始事物
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($model->status == StatusEnum::DELETE) {
                return ResultHelper::json(422, '请不要重复操作');
            }

            $model->status = StatusEnum::DELETE;
            $model->save();

            // 回调
            $this->callBack($model, $class, -1);

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            // 返回数据验证失败
            return ResultHelper::json(422, $e->getMessage());
        }

        return true;
    }

    /**
     * 权限验证
     *
     * @param string $action 当前的方法
     * @param null $model 当前的模型类
     * @param array $params $_GET变量
     * @throws \yii\web\BadRequestHttpException
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        // 方法名称
        if (in_array($action, ['index', 'update', 'view'])) {
            throw new \yii\web\BadRequestHttpException('权限不足');
        }
    }

    /**
     * @param $topic_id
     * @param $topic_type
     * @return mixed
     */
    abstract function findByTopicId($topic_id, $topic_type);

    /**
     * @param $id
     * @return mixed
     */
    abstract function findById($id);

    /**
     * @param $model
     * @param $class
     * @param $num
     * @return mixed
     */
    abstract function callBack($model, $class, $num);
}