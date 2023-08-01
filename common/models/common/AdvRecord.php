<?php

namespace addons\TinyShop\common\models\common;

use Yii;

/**
 * This is the model class for table "{{%addon_tiny_shop_common_popup_adv_record}}".
 *
 * @property int $id 序号
 * @property int|null $member_id 用户id
 * @property int|null $merchant_id 商户id
 * @property int|null $popup_adv_id 弹出广告id
 * @property string|null $ip ip地址
 * @property string|null $device_id 设备ID
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class AdvRecord extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_common_popup_adv_record}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['member_id', 'merchant_id', 'popup_adv_id', 'created_at', 'updated_at'], 'integer'],
            [['ip'], 'string', 'max' => 50],
            [['device_id'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '序号',
            'member_id' => '用户id',
            'merchant_id' => '商户id',
            'popup_adv_id' => '弹出广告id',
            'ip' => 'ip地址',
            'device_id' => '设备ID',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
