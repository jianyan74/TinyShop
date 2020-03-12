<?php

namespace addons\TinyShop\common\models\common;

use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%backend_notify_pull_time}}".
 *
 * @property int $member_id
 * @property int $type 消息类型[1:公告;2:提醒;3:信息(私信)
 * @property string $alert_type 提醒消息类型[backend:系统;wechat:微信]
 * @property int $last_time 最后拉取时间
 */
class NotifyPullTime extends \yii\db\ActiveRecord
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_base_notify_pull_time}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['member_id'], 'required'],
            [['member_id', 'merchant_id', 'type', 'last_time'], 'integer'],
            [['alert_type'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'member_id' => 'member ID',
            'type' => '消息类型',
            'alert_type' => '提醒消息类型',
            'last_time' => '最后拉取时间',
        ];
    }
}
