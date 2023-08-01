<?php

namespace addons\TinyShop\common\models\common;

use Yii;

/**
 * This is the model class for table "{{%addon_tiny_shop_common_notify_pull_time}}".
 *
 * @property int $id
 * @property int $member_id 管理员id
 * @property int|null $merchant_id 商户id
 * @property int|null $type 消息类型[1:公告;2:提醒;3:信息(私信)
 * @property string|null $alert_type 提醒消息类型[sys:系统;wechat:微信]
 * @property int|null $last_time 最后拉取时间
 * @property int|null $last_id 最后拉取ID
 */
class NotifyPullTime extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_common_notify_pull_time}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['member_id'], 'required'],
            [['member_id', 'merchant_id', 'type', 'last_time', 'last_id'], 'integer'],
            [['alert_type'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => '管理员id',
            'merchant_id' => '商户id',
            'type' => '消息类型[1:公告;2:提醒;3:信息(私信)',
            'alert_type' => '提醒消息类型[sys:系统;wechat:微信]',
            'last_time' => '最后拉取时间',
            'last_id' => '最后拉取ID',
        ];
    }
}
