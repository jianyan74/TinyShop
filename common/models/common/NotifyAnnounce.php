<?php

namespace addons\TinyShop\common\models\common;

use Yii;
use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%addon_tiny_shop_common_notify_announce}}".
 *
 * @property int $id 主键
 * @property int|null $member_id 用户id
 * @property int|null $merchant_id 商户id
 * @property string|null $title 标题
 * @property string|null $content 消息内容
 * @property string|null $cover 封面
 * @property string|null $synopsis 概要
 * @property int|null $view 浏览量
 * @property int|null $status 状态[-1:删除;0:禁用;1启用]
 * @property int|null $created_at 创建时间
 * @property int|null $updated_at 修改时间
 */
class NotifyAnnounce extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_common_notify_announce}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'content', 'synopsis'], 'required'],
            [['member_id', 'merchant_id', 'view', 'status', 'created_at', 'updated_at'], 'integer'],
            [['content'], 'string'],
            [['title'], 'string', 'max' => 150],
            [['cover'], 'string', 'max' => 100],
            [['synopsis'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键',
            'member_id' => '用户id',
            'merchant_id' => '商户id',
            'title' => '标题',
            'content' => '内容',
            'cover' => '封面',
            'synopsis' => '概要',
            'view' => '浏览量',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        Yii::$app->tinyShopService->notify->createAnnounce($this->title, $this->status, $this->id);

        parent::afterSave($insert, $changedAttributes);
    }
}
