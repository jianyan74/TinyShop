<?php

namespace addons\TinyShop\common\models\common;

use Yii;

/**
 * This is the model class for table "{{%addon_tiny_shop_common_notify_subscription_config}}".
 *
 * @property int $member_id 用户id
 * @property string|null $app_id 应用id
 * @property string|null $action 订阅事件
 * @property int|null $merchant_id 商户id
 */
class NotifySubscriptionConfig extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_common_notify_subscription_config}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['member_id'], 'required'],
            [['member_id', 'merchant_id'], 'integer'],
            [['action'], 'safe'],
            [['app_id'], 'string', 'max' => 50],
            [['member_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'member_id' => '用户id',
            'app_id' => '应用id',
            'action' => '订阅事件',
            'merchant_id' => '商户id',
        ];
    }
}
