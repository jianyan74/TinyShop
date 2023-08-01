<?php

namespace addons\TinyShop\common\models\common;

use yii\helpers\Json;
use common\helpers\StringHelper;
use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%addon_tiny_shop_common_adv}}".
 *
 * @property int $id 序号
 * @property int|null $merchant_id 商户id
 * @property string $name 标题
 * @property string|null $cover 图片
 * @property string|null $location 广告位ID
 * @property int|null $view 浏览量
 * @property string|null $describe 图片描述
 * @property int|null $start_time 开始时间
 * @property int|null $end_time 结束时间
 * @property string|null $jump_link 跳转链接
 * @property string|null $jump_type 跳转方式
 * @property string|null $extend_link 跳转链接
 * @property int|null $sort 优先级
 * @property int|null $status 状态
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class Adv extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_common_adv}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'cover', 'start_time', 'end_time'], 'required'],
            [['merchant_id', 'view', 'sort', 'status', 'created_at', 'updated_at'], 'integer', 'min' => 0],
            [['extend_link'], 'safe'],
            [['name'], 'string', 'max' => 50],
            [['cover'], 'string', 'max' => 200],
            [['end_time'], 'comparisonEndTime'],
            [['location', 'jump_type'], 'string', 'max' => 30],
            [['describe', 'jump_link'], 'string', 'max' => 150],
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
            'cover' => '轮播图',
            'location' => '广告位置',
            'view' => '浏览量',
            'describe' => '图片描述',
            'start_time' => '开始时间',
            'end_time' => '结束时间',
            'jump_link' => '跳转链接',
            'jump_type' => '跳转方式',
            'extend_link' => '跳转链接',
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
