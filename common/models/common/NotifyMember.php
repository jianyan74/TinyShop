<?php

namespace addons\TinyShop\common\models\common;

use Yii;

/**
 * This is the model class for table "{{%addon_tiny_shop_common_notify_member}}".
 *
 * @property int $id
 * @property string|null $app_id 应用id
 * @property int|null $merchant_id 商户id
 * @property int $member_id 管理员id
 * @property int|null $notify_id 消息id
 * @property int|null $is_read 是否已读 1已读
 * @property int|null $type 消息类型[1:公告;2:提醒;3:信息(私信)
 * @property int $status 状态[-1:删除;0:禁用;1启用]
 * @property int $created_at 创建时间
 * @property int|null $updated_at 修改时间
 */
class NotifyMember extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_common_notify_member}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'member_id', 'notify_id', 'is_read', 'type', 'status', 'created_at', 'updated_at'], 'integer'],
            [['app_id'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'app_id' => '应用id',
            'merchant_id' => '商户id',
            'member_id' => '管理员id',
            'notify_id' => '消息id',
            'is_read' => '是否已读 1已读',
            'type' => '消息类型[1:公告;2:提醒;3:信息(私信)',
            'status' => '状态[-1:删除;0:禁用;1启用]',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotify()
    {
        return $this->hasOne(Notify::class, ['id' => 'notify_id']);
    }
}
