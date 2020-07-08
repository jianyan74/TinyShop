<?php

namespace addons\TinyShop\common\models\marketing;

use Yii;

/**
 * This is the model class for table "{{%addon_shop_marketing_mini_program_live_replay}}".
 *
 * @property int $id id
 * @property int $merchant_id 商户id
 * @property int $live_id 关联id
 * @property string $media_url 回放视频
 * @property int $expire_time 回放视频 url 过期时间
 * @property int $create_time 回放视频创建时间
 * @property int $status 状态[-1:删除;0:禁用;1启用]
 * @property int $created_at 创建时间
 * @property int $updated_at 修改时间
 */
class MiniProgramLiveReplay extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_marketing_mini_program_live_replay}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'live_id', 'expire_time', 'create_time', 'status', 'created_at', 'updated_at'], 'integer'],
            [['media_url'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'merchant_id' => '商户id',
            'live_id' => '关联id',
            'media_url' => '回放视频',
            'expire_time' => '回放视频 url 过期时间',
            'create_time' => '回放视频创建时间',
            'status' => '状态[-1:删除;0:禁用;1启用]',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }
}
