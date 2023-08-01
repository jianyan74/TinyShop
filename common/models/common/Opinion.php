<?php

namespace addons\TinyShop\common\models\common;

use common\behaviors\MerchantBehavior;
use common\traits\HasOneMember;
use Yii;

/**
 * This is the model class for table "{{%addon_tiny_shop_common_opinion}}".
 *
 * @property int $id
 * @property int|null $merchant_id 商户id
 * @property int $member_id 用户id
 * @property string $content 内容
 * @property string|null $covers 反馈图片
 * @property string|null $contact_way 联系方式
 * @property string|null $reply 回复
 * @property int|null $type 反馈类型
 * @property string|null $from 来源
 * @property int|null $sort 优先级（0-9）
 * @property int|null $status 状态[-1:删除;0:禁用;1启用]
 * @property int|null $created_at 创建时间
 * @property int|null $updated_at 更新时间
 */
class Opinion extends \common\models\base\BaseModel
{
    use MerchantBehavior, HasOneMember;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_common_opinion}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'member_id', 'type', 'sort', 'status', 'created_at', 'updated_at'], 'integer'],
            [['content', 'type'], 'required'],
            [['content'], 'string'],
            [['covers'], 'safe'],
            [['contact_way'], 'string', 'max' => 50],
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
            'merchant_id' => '商户id',
            'member_id' => '用户id',
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
