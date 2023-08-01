<?php

namespace addons\TinyShop\api\modules\v1\controllers\member;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use api\controllers\UserAuthController;
use common\enums\StatusEnum;
use common\enums\NotifyTypeEnum;
use addons\TinyShop\common\models\common\NotifyMember;

/**
 * Class NotifyController
 * @package addons\TinyShop\api\modules\v1\controllers\member
 * @author jianyan74 <751393839@qq.com>
 */
class NotifyController extends UserAuthController
{
    /**
     * @var NotifyMember
     */
    public $modelClass = NotifyMember::class;

    /**
     * @return array
     */
    public function actionIndex()
    {
        $type = Yii::$app->request->post('type', NotifyTypeEnum::REMIND);
        $member_id = Yii::$app->user->identity->member_id;
        $chat = [];
        $unReadCount = 0;
        if (Yii::$app->has('tinyChatService')) {
            $chat = Yii::$app->tinyChatService->conversation->findNewestByMemberId($member_id);
            $unReadCount = Yii::$app->tinyChatService->bubble->unReadCount($member_id);
        }

        return [
            'chat' => [
                'newest' => $chat,
                'unReadCount' => $unReadCount
            ],
            'list' => new ActiveDataProvider([
                'query' => $this->modelClass::find()
                    ->where(['status' => StatusEnum::ENABLED])
                    ->andWhere(['type' => $type, 'member_id' => $member_id])
                    ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                    ->with(['notify'])
                    ->orderBy('id desc')
                    ->asArray(),
                'pagination' => [
                    'pageSize' => $this->pageSize,
                    'validatePage' => false,// 超出分页不返回data
                ],
            ]),
        ];
    }

    /**
     * @return array
     */
    public function actionNewest()
    {
        $memberId = Yii::$app->user->identity->member_id;

        $unReadCount = Yii::$app->tinyShopService->notifyMember->unReadCount($memberId);
        $newest = Yii::$app->tinyShopService->notifyMember->newest($memberId);

        return [
            'newest' => $newest,
            'unReadCount' => $unReadCount
        ];
    }

    /**
     * @param $id
     * @return \yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $member_id = Yii::$app->user->identity->member_id;
        /* @var $model \yii\db\ActiveRecord */
        if (empty($id) || !($model = $this->modelClass::find()->where([
                'id' => $id,
                'status' => StatusEnum::ENABLED,
                'member_id' => $member_id
            ])->andFilterWhere(['merchant_id' => $this->getMerchantId()])->with(['notify'])->asArray()->one())) {
            throw new NotFoundHttpException('请求的数据不存在');
        }

        // 设置为已读
        Yii::$app->tinyShopService->notifyMember->read($member_id, [$model['notify_id']]);

        return $model;
    }

    /**
     * 未读数量
     *
     * @return mixed
     */
    public function actionUnReadCount()
    {
        $member_id = Yii::$app->user->identity->member_id;

        return Yii::$app->tinyShopService->notifyMember->unReadCount($member_id);
    }

    /**
     * 单个已读
     *
     * @param $notify_id
     */
    public function actionRead($notify_id)
    {
        // 设置为已读
        $member_id = Yii::$app->user->identity->member_id;
        Yii::$app->tinyShopService->notifyMember->read($member_id, [$notify_id]);

        return true;
    }

    /**
     * 删除多个
     *
     * @return mixed
     */
    public function actionClear()
    {
        $notify_ids = Yii::$app->request->post('notify_ids');
        $notify_ids = explode(',', $notify_ids);
        $member_id = Yii::$app->user->identity->member_id;

        Yii::$app->tinyShopService->notifyMember->clear($member_id, $notify_ids);

        return true;
    }

    /**
     * 清空
     *
     * @return mixed
     */
    public function actionClearAll()
    {
        $type = Yii::$app->request->post('type', NotifyTypeEnum::REMIND);
        $member_id = Yii::$app->user->identity->member_id;

        Yii::$app->tinyShopService->notifyMember->clearAll($member_id, $type);

        return true;
    }

    /**
     * 全部已读
     *
     * @return mixed
     */
    public function actionReadAll()
    {
        // 设置为已读
        $member_id = Yii::$app->user->identity->member_id;

        Yii::$app->tinyShopService->notifyMember->readAll($member_id);

        return true;
    }
}
