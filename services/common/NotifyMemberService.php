<?php

namespace addons\TinyShop\services\common;

use common\enums\StatusEnum;
use common\enums\NotifyTypeEnum;
use addons\TinyShop\common\models\common\NotifyMember;

/**
 * Class NotifyMemberService
 * @package addons\TinyShop\services\common
 * @author jianyan74 <751393839@qq.com>
 */
class NotifyMemberService
{
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
                case NotifyTypeEnum::ANNOUNCE :
                    $data['announce'] += $group['count'];
                    break;
                case NotifyTypeEnum::REMIND :
                    $data['remind'] += $group['count'];
                    break;
                case NotifyTypeEnum::MESSAGE :
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
        NotifyMember::updateAll(['is_read' => true, 'updated_at' => time()], ['and', ['member_id' => $member_id], ['in', 'notify_id', $notifyIds]]);
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
            'updated_at' => time(),
        ], [
                'and',
                ['member_id' => $member_id],
                ['in', 'notify_id', $notifyIds],
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
            'updated_at' => time(),
        ], [
            'member_id' => $member_id,
            'type' => $type,
        ]);
    }

    /**
     * 全部设为已读
     *
     * @param $member_id
     */
    public function readAll($member_id)
    {
        NotifyMember::updateAll(['is_read' => true, 'updated_at' => time()], ['member_id' => $member_id, 'is_read' => false]);
    }

    /**
     * @param $member_id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function newest($member_id)
    {
        return NotifyMember::find()->where(['status' => StatusEnum::ENABLED])
            ->andWhere([
                'type' => NotifyTypeEnum::REMIND,
                'member_id' => $member_id,
                'is_read' => StatusEnum::DISABLED
            ])
            ->with(['notify'])
            ->orderBy('id desc')
            ->asArray()
            ->one();
    }
}
