<?php

namespace addons\TinyShop\common\models\common;

use Yii;

/**
 * This is the model class for table "{{%addon_tiny_shop_common_attribute_value}}".
 *
 * @property int $id 属性值ID
 * @property int|null $merchant_id 商户id
 * @property int|null $attribute_id 属性ID
 * @property string|null $title 属性值名称
 * @property string|null $value 属性对应相关数据
 * @property int|null $type 属性对应输入类型1.直接2.单选3.多选
 * @property int|null $sort 排序
 * @property int|null $status 状态(-1:已删除,0:禁用,1:正常)
 * @property int|null $created_at 创建时间
 * @property int|null $updated_at 修改时间
 */
class AttributeValue extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_common_attribute_value}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'attribute_id', 'type', 'sort', 'status', 'created_at', 'updated_at'], 'integer'],
            [['title'], 'string', 'max' => 50],
            [['value'], 'string', 'max' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '属性值ID',
            'merchant_id' => '商户id',
            'attribute_id' => '属性ID',
            'title' => '属性值名称',
            'value' => '属性对应相关数据',
            'type' => '属性对应输入类型1.直接2.单选3.多选',
            'sort' => '排序',
            'status' => '状态(-1:已删除,0:禁用,1:正常)',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }
}
