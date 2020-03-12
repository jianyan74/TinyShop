<?php

namespace addons\TinyShop\common\models\common;

use common\behaviors\MerchantBehavior;

/**
 *
 * @property string $id
 * @property string $merchant_id 商户id
 * @property int $member_id 用户id
 * @property int $topic_id 主题id
 * @property string $topic_type 主题类型
 * @property int $status 状态[-1:删除;0:禁用;1启用]
 * @property string $created_at 创建时间
 * @property string $updated_at 修改时间
 */
class Follow extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['topic_type', 'topic_id', 'member_id'], 'required'],
            [['merchant_id', 'member_id', 'topic_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['topic_type'], 'string', 'max' => 50],
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
            'topic_id' => '主题id',
            'topic_type' => '主题类型',
            'status' => '状态[-1:删除;0:禁用;1启用]',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }
}
