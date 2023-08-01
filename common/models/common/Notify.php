<?php

namespace addons\TinyShop\common\models\common;

use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%addon_tiny_shop_common_notify}}".
 *
 * @property int $id 主键
 * @property int|null $merchant_id 商户id
 * @property string|null $title 标题
 * @property string|null $content 消息内容
 * @property int|null $type 消息类型[1:公告;2:提醒;3:信息(私信)
 * @property int|null $target_id 目标id
 * @property string|null $target_type 目标类型
 * @property int|null $target_display 目标者是否删除
 * @property string|null $action 动作
 * @property int|null $view 浏览量
 * @property int|null $sender_id 发送者id
 * @property int|null $sender_display 发送者是否删除
 * @property int|null $sender_revocation 是否撤回 0是撤回
 * @property string|null $params 参数
 * @property int $status 状态[-1:删除;0:禁用;1启用]
 * @property int $created_at 创建时间
 * @property int|null $updated_at 修改时间
 */
class Notify extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_common_notify}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'type', 'target_id', 'target_display', 'view', 'sender_id', 'sender_display', 'sender_revocation', 'status', 'created_at', 'updated_at'], 'integer'],
            [['params'], 'safe'],
            [['title'], 'string', 'max' => 150],
            [['content'], 'string', 'max' => 300],
            [['target_type', 'action'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键',
            'merchant_id' => '商户id',
            'title' => '标题',
            'content' => '消息内容',
            'type' => '消息类型[1:公告;2:提醒;3:信息(私信)',
            'target_id' => '目标id',
            'target_type' => '目标类型',
            'target_display' => '目标者是否删除',
            'action' => '动作',
            'view' => '浏览量',
            'sender_id' => '发送者id',
            'sender_display' => '发送者是否删除',
            'sender_revocation' => '是否撤回 0是撤回',
            'params' => '参数',
            'status' => '状态[-1:删除;0:禁用;1启用]',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }
}
