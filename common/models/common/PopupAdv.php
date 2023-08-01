<?php

namespace addons\TinyShop\common\models\common;

use yii\helpers\Json;
use common\behaviors\MerchantBehavior;
use common\helpers\StringHelper;

/**
 * This is the model class for table "{{%addon_tiny_shop_common_popup_adv}}".
 *
 * @property int $id 序号
 * @property int|null $merchant_id 商户id
 * @property string $name 标题
 * @property string|null $cover 图片
 * @property int|null $view 浏览量
 * @property int|null $start_time 开始时间
 * @property int|null $end_time 结束时间
 * @property string|null $jump_link 跳转链接
 * @property string|null $jump_type 跳转方式
 * @property string|null $extend_link 跳转链接
 * @property int|null $popup_mode 弹出方式 1:首次弹出;2:每次弹出
 * @property int|null $popup_type 弹出类型 1:图片;2:优惠券
 * @property int|null $sort 优先级
 * @property int|null $status 状态
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class PopupAdv extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_common_popup_adv}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['start_time', 'end_time', 'name', 'cover'], 'required'],
            [['merchant_id', 'view', 'popup_mode', 'popup_type', 'sort', 'status', 'created_at', 'updated_at'], 'integer', 'min' => 0],
            [['extend_link'], 'safe'],
            [['name'], 'string', 'max' => 50],
            [['cover'], 'string', 'max' => 200],
            [['jump_link'], 'string', 'max' => 150],
            [['jump_type'], 'string', 'max' => 30],
            [['end_time'], 'comparisonEndTime'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '序号',
            'merchant_id' => '商户id',
            'name' => '标题',
            'cover' => '图片',
            'view' => '浏览量',
            'start_time' => '开始时间',
            'end_time' => '结束时间',
            'jump_link' => '跳转链接',
            'jump_type' => '跳转方式',
            'extend_link' => '跳转链接',
            'popup_mode' => '弹出方式', // 1:首次弹出;2:每次弹出
            'popup_type' => '弹出类型', // 1:图片;2:优惠券
            'sort' => '优先级',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * @param $attribute
     */
    public function comparisonEndTime($attribute)
    {
        $start_time = StringHelper::dateToInt($this->start_time);
        $end_time = StringHelper::dateToInt($this->end_time);

        if ($start_time >= $end_time) {
            $this->addError($attribute, '结束时间必须大于开始时间');
        }
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        $this->start_time = StringHelper::dateToInt($this->start_time);
        $this->end_time = StringHelper::dateToInt($this->end_time);
        if (!empty($this->extend_link) && !is_array($this->extend_link)) {
            $this->extend_link = Json::decode($this->extend_link);
        }

        return parent::beforeSave($insert);
    }
}
