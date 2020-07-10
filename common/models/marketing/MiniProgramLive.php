<?php

namespace addons\TinyShop\common\models\marketing;

use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%addon_shop_marketing_mini_program_live}}".
 *
 * @property int $id id
 * @property int $merchant_id 商户id
 * @property string $name 直播房间名
 * @property int $room_id 房间ID
 * @property string $cover 直播封面
 * @property int $live_status 直播状态
 * @property int $start_time 开始时间
 * @property int $end_time 结束时间
 * @property string $anchor_name 主播名称
 * @property string $share_img 主播头像
 * @property int $status 状态[-1:删除;0:禁用;1启用]
 * @property int $created_at 创建时间
 * @property int $updated_at 修改时间
 */
class MiniProgramLive extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_marketing_mini_program_live}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'is_recommend', 'is_stick', 'room_id', 'live_status', 'start_time', 'end_time', 'status', 'created_at', 'updated_at'], 'integer'],
            [['name', 'cover', 'anchor_name', 'share_img'], 'string', 'max' => 200],
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
            'name' => '直播房间名',
            'room_id' => '房间ID',
            'cover' => '直播封面',
            'live_status' => '直播状态',
            'start_time' => '开始时间',
            'end_time' => '结束时间',
            'anchor_name' => '主播名称',
            'share_img' => '分享卡片封面',
            'is_recommend' => '推荐',
            'is_stick' => '置顶',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGoods()
    {
        return $this->hasMany(MiniProgramLiveGoods::class, ['live_id' => 'id']);
    }
}
