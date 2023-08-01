<?php

namespace addons\TinyShop\api\modules\v1\controllers\member;

use Yii;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use api\controllers\UserAuthController;
use common\enums\StatusEnum;
use common\helpers\ResultHelper;
use addons\TinyShop\common\models\common\Opinion;
use addons\TinyShop\common\enums\SubscriptionActionEnum;

/**
 * 意见反馈
 *
 * Class OpinionController
 * @package addons\TinyShop\api\modules\v1\controllers\member
 * @author jianyan74 <751393839@qq.com>
 */
class OpinionController extends UserAuthController
{
    /**
     * @var Opinion
     */
    public $modelClass = Opinion::class;

    /**
     * @return array|mixed|\yii\db\ActiveRecord
     */
    public function actionCreate()
    {
        /* @var $model \yii\db\ActiveRecord */
        $model = new $this->modelClass();
        $model->attributes = Yii::$app->request->post();
        $model->from = Yii::$app->user->identity->group;
        $model->member_id = Yii::$app->user->identity->member_id;
        $model->merchant_id = Yii::$app->user->identity->merchant_id;
        if (!$model->save()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        // 意见反馈
        Yii::$app->tinyShopService->notify->createRemind(
            $model->id,
            SubscriptionActionEnum::OPINION_CREATE,
            $model->merchant_id,
            ['opinion' => $model]
        );

        return $model;
    }

    /**
     * 单个显示
     *
     * @param $id
     * @return \yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        !is_array($model->covers) && $model->covers = Json::decode($model->covers);

        return $model;
    }

    /**
     * @return array
     */
    public function actionType()
    {
        return [
            1 => '功能建议',
            2 => 'BUG反馈',
            3 => '业务咨询',
        ];
    }
}
