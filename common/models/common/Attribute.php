<?php

namespace addons\TinyShop\common\models\common;

use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%addon_tiny_shop_common_attribute}}".
 *
 * @property int $id 商品属性ID
 * @property int|null $merchant_id 商户id
 * @property string $title 模型名称
 * @property int|null $sort 排序
 * @property int $status 状态(-1:已删除,0:禁用,1:正常)
 * @property int|null $created_at 创建时间
 * @property int|null $updated_at 修改时间
 */
class Attribute extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_common_attribute}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'sort', 'status', 'created_at', 'updated_at'], 'integer'],
            [['title'], 'string', 'max' => 50],
            [['title'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '商品属性ID',
            'merchant_id' => '商户id',
            'title' => '参数名称',
            'sort' => '排序',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
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

    public function afterDelete()
    {
        AttributeValue::deleteAll(['merchant_id' => $this->merchant_id, 'attribute_id' => $this->id]);

        parent::afterDelete();
    }
}
