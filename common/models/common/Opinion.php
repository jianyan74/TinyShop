<?php

namespace addons\TinyShop\common\models\common;

use addons\TinyShop\common\traits\HasOneMember;
use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%addon_shop_base_opinion}}".
 *
 * @property int $id
 * @property int $member_id 用户id
 * @property int $merchant_id 商户id
 * @property string $content 内容
 * @property array $covers 反馈图片
 * @property string $reply 回复
 * @property string $from 来源
 * @property int $type 反馈类型
 * @property int $sort 优先级（0-9）
 * @property int $status 状态[-1:删除;0:禁用;1启用]
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class Opinion extends \common\models\base\BaseModel
{
    use MerchantBehavior, HasOneMember;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_base_opinion}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['member_id', 'merchant_id', 'type', 'sort', 'status', 'created_at', 'updated_at'], 'integer'],
            [['content', 'type'], 'required'],
            [['content'], 'string'],
            [['covers'], 'safe'],
            [['contact_way'], 'string', 'max' => 100],
            [['reply', 'from'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => '用户id',
            'merchant_id' => '商户id',
            'content' => '内容',
            'covers' => '反馈图片',
            'contact_way' => '联系方式',
            'reply' => '回复',
            'type' => '反馈类型',
            'from' => '来源',
            'sort' => '优先级（0-9）',
            'status' => '状态[-1:删除;0:禁用;1启用]',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
