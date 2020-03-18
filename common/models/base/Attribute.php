<?php

namespace addons\TinyShop\common\models\base;

use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%addon_shop_base_attribute}}".
 *
 * @property string $id 商品属性ID
 * @property string $merchant_id 商户id
 * @property string $title 模型名称
 * @property int $sort 排序
 * @property string $spec_ids 关联规格ids
 * @property int $status 状态(-1:已删除,0:禁用,1:正常)
 * @property string $created_at 创建时间
 * @property string $updated_at 修改时间
 */
class Attribute extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    public $valueData;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_base_attribute}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'sort', 'status', 'created_at', 'updated_at'], 'integer'],
            [['title'], 'string', 'max' => 50],
            [['spec_ids'], 'safe'],
            ['valueData', 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'merchant_id' => 'Merchant ID',
            'title' => '标题',
            'sort' => '排序',
            'spec_ids' => '规格id组',
            'status' => '状态',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * 关联属性值
     *
     * @return \yii\db\ActiveQuery
     */
    public function getValue()
    {
        return $this->hasMany(AttributeValue::class, ['attribute_id' => 'id']);
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     * @throws \yii\db\Exception
     */
    public function afterSave($insert, $changedAttributes)
    {
        AttributeValue::updateData($this->valueData, $this->value, $this->id, $this->merchant_id);
        parent::afterSave($insert, $changedAttributes);
    }

    public function afterDelete()
    {
        AttributeValue::deleteAll(['merchant_id' => $this->merchant_id, 'attribute_id' => $this->id]);
        parent::afterDelete();
    }
}
