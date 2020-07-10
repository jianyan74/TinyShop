<?php

namespace addons\TinyShop\services\common;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use yii\data\Pagination;
use common\enums\StatusEnum;
use common\components\Service;
use addons\TinyShop\common\models\common\Notify;
use addons\TinyShop\common\models\common\NotifyMember;
use addons\TinyShop\common\enums\AccessTokenGroupEnum;
use addons\TinyShop\common\enums\SubscriptionActionEnum;

//use common\enums\SubscriptionAlertTypeEnum;
//use common\models\backend\NotifySubscriptionConfig;

/**
 * 消息通知
 *
 * Class NotifyService
 * @package addons\TinyShop\services\common
 * @author jianyan74 <751393839@qq.com>
 */
class NotifyService extends Service
{
    /**
     * 创建公告
     *
     * @param string $content
     * @param int $sender_id
     */
    public function createAnnounce($content, $sender_id)
    {
        $model = new Notify();
        $model->content = $content;
        $model->sender_id = $sender_id;
        $model->type = Notify::TYPE_ANNOUNCE;

        return $model->save();
    }

    /**
     * 创建提醒
     *
     * @param int $target_id 触发id
     * @param string $targetType 触发类型
     * @param string $action 提醒关联动作
     * @param int $sender_id 发送者(用户)id
     * @param string $content 内容
     */
    public function createRemind($target_id, $targetType, $sender_id, $content)
    {
        $model = new Notify();
        $model->target_id = $target_id;
        $model->target_type = $targetType;
        $model->action = $targetType;
        $model->content = $content;
        $model->sender_id = $sender_id;
        $model->type = Notify::TYPE_REMIND;

        return $model->save();
    }

    /**
     * 创建提醒
     *
     * @param $target_id
     * @param $targetType
     * @param $receiver_id
     * @param string $content
     * @return bool
     */
    public function createRemindByReceiver($target_id, $targetType, $receiver_id, $content = '', $params = [])
    {

    }

    /**
     * 创建一条信息(私信)
     *
     * @param int $sender_id 触发id
     * @param string $content 内容
     * @param int $receiver 接收id
     */
    public function createMessage($content, $sender_id, $receiver)
    {
        $model = new Notify();
        $model->content = $content;
        $model->sender_id = $sender_id;
        $model->type = Notify::TYPE_MESSAGE;
        if ($model->save()) {
            $NotifyMember = new NotifyMember();
            $NotifyMember->notify_id = $model->id;
            $NotifyMember->member_id = $receiver;
            $NotifyMember->type = Notify::TYPE_MESSAGE;

            return $NotifyMember->save();
        }

        return false;
    }

    /**
     * 小程序订阅消息提醒
     *
     * @param Notify $model
     * @param $oauth_client_user_id
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function miniProgramRemind(Notify $model, $oauth_client_user_id)
    {

    }

    /**
     * 微信消息模板提醒
     *
     * @param Notify $model
     * @param $oauth_client_user_id
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function wechatRemind(Notify $model, $oauth_client_user_id)
    {

    }

    /**
     * 拉取公告
     *
     * @param int $member_id 用户id
     * @throws \yii\db\Exception
     */
    public function pullAnnounce($member_id, $created_at)
    {
        // 从UserNotify中获取最近的一条公告信息的创建时间: lastTime
        $model = NotifyMember::find()
            ->where(['member_id' => $member_id, 'type' => Notify::TYPE_ANNOUNCE])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->orderBy('id desc')
            ->asArray()
            ->one();

        // 用lastTime作为过滤条件，查询Notify的公告信息
        $lastTime = $model ? $model['created_at'] : $created_at;
        $notifys = Notify::find()
            ->where(['type' => Notify::TYPE_ANNOUNCE, 'status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->andWhere(['>', 'created_at', $lastTime])
            ->asArray()
            ->all();

        // 新建UserNotify并关联查询出来的公告信息
        $rows = [];
        $fields = ['notify_id', 'member_id', 'type', 'created_at', 'updated_at'];
        foreach ($notifys as $notify) {
            $rows[] = [$notify['id'], $member_id, Notify::TYPE_ANNOUNCE, $notify['created_at'], time()];
        }

        !empty($rows) && Yii::$app->db->createCommand()->batchInsert(NotifyMember::tableName(), $fields,
            $rows)->execute();
    }

    /**
     * 拉取提醒
     *
     * @param NotifySubscriptionConfig $subscriptionConfig
     * @param string $type
     */
    public function pullRemind(NotifySubscriptionConfig $subscriptionConfig, $type = SubscriptionAlertTypeEnum::SYS)
    {
        /** @var array $action 查询用户的配置文件SubscriptionConfig */
        $action = $subscriptionConfig->action;
        !is_array($action) && $action = [];

        $filt = [];
        foreach ($action as $key => $item) {
            // 默认拉取系统通知
            if ($key == $type) {
                foreach ($item as $index => $value) {
                    $value == true && $filt[] = $index;
                }
            }
        }

        // 查询最后的一条提醒时间
        $lastTime = Yii::$app->services->backendNotifyPullTime->getLastTime($subscriptionConfig->member_id,
            Notify::TYPE_REMIND, $type);
        // 直接通过自己的关注去拉取消息
        $notifys = Notify::find()
            ->where(['type' => Notify::TYPE_REMIND, 'status' => StatusEnum::ENABLED])
            ->andWhere(['in', 'action', $filt])
            ->andWhere(['>', 'created_at', $lastTime])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->asArray()
            ->all();

        // 使用过滤好的Notify作为关联新建UserNotify
        foreach ($notifys as $notify) {
            $NotifyMember = new NotifyMember();
            $NotifyMember->notify_id = $notify['id'];
            $NotifyMember->member_id = $subscriptionConfig->member_id;
            $NotifyMember->type = Notify::TYPE_REMIND;
            $NotifyMember->save();
        }

        return $notifys;
    }

    /**
     * 更新订阅配置
     *
     * @param $member_id
     */
    public function updateSubscriptionConfig($member_id)
    {
        $actions = [];
        $config = NotifySubscriptionConfig::findOne(['member_id' => $member_id]);
        $config->action = Json::encode($actions);

        return $config->save();
    }

    /**
     * 获取用户消息列表
     *
     * @param $member_id
     */
    public function getUserNotify($member_id, $is_read = 0)
    {
        $data = NotifyMember::find()
            ->where(['status' => StatusEnum::ENABLED, 'is_read' => $is_read])
            ->andWhere(['member_id' => $member_id])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()]);
        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => 10]);
        $models = $data->offset($pages->offset)
            ->with('notify')
            ->orderBy('id desc')
            ->limit($pages->limit)
            ->asArray()
            ->all();

        foreach ($models as &$model) {
            $model['type'] = Notify::$typeExplain[$model['type']];
        }

        return [$models, $pages];
    }

    /**
     * 获取公告
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getAnnounce()
    {
        return Notify::find()
            ->select(['id', 'title', 'cover', 'synopsis', 'view', 'created_at'])
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere(['type' => Notify::TYPE_ANNOUNCE])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->orderBy('id desc')
            ->cache(30)
            ->asArray()
            ->all();
    }

    /**
     * 未读数量(组别)
     *
     * @param $member_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function unReadCount($member_id)
    {
        $data = [
            'count' => 0,
            'announce' => 0,
            'remind' => 0,
            'message' => 0,
        ];

        $groups = NotifyMember::find()
            ->select(['type', 'count(id) as count'])
            ->where(['member_id' => $member_id, 'is_read' => 0])
            ->groupBy(['type'])
            ->asArray()
            ->all();

        foreach ($groups as $group) {
            $data['count'] += $group['count'];

            switch ($group['type']) {
                case Notify::TYPE_ANNOUNCE :
                    $data['announce'] += $group['count'];
                    break;
                case Notify::TYPE_REMIND :
                    $data['remind'] += $group['count'];
                    break;
                case Notify::TYPE_MESSAGE :
                    $data['message'] += $group['count'];
                    break;
            }
        }

        return $data;
    }

    /**
     * 更新指定的notify，把isRead属性设置为true
     *
     * @param $member_id
     */
    public function read($member_id, $notifyIds)
    {
        NotifyMember::updateAll(['is_read' => true, 'updated_at' => time()],
            ['and', ['member_id' => $member_id], ['in', 'notify_id', $notifyIds]]);
    }

    /**
     * 清空
     *
     * @param $member_id
     * @param $type
     */
    public function clear($member_id, $notifyIds)
    {
        NotifyMember::updateAll([
            'is_read' => true,
            'status' => StatusEnum::DELETE,
            'updated_at' => time()
        ], [
                'and',
                ['member_id' => $member_id],
                ['in', 'notify_id', $notifyIds]
            ]
        );
    }

    /**
     * 清空
     *
     * @param $member_id
     * @param $type
     */
    public function clearAll($member_id, $type)
    {
        NotifyMember::updateAll([
            'is_read' => true,
            'status' => StatusEnum::DELETE,
            'updated_at' => time()
        ], [
            'member_id' => $member_id,
            'type' => $type
        ]);
    }

    /**
     * 全部设为已读
     *
     * @param $member_id
     */
    public function readAll($member_id)
    {
        NotifyMember::updateAll(['is_read' => true, 'updated_at' => time()],
            ['member_id' => $member_id, 'is_read' => false]);
    }
}