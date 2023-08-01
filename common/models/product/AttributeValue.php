<?php

namespace addons\TinyShop\common\models\product;

use Yii;

/**
 * This is the model class for table "{{%addon_tiny_shop_product_attribute_value}}".
 *
 * @property int $id
 * @property int|null $merchant_id 商户id
 * @property int $product_id 商品编码
 * @property string $title 参数名称
 * @property string $value 参数值
 * @property int|null $type 属性对应输入类型1.直接2.单选3.多选
 * @property int $sort 排序
 * @property string|null $data
 * @property int $status 状态(-1:已删除,0:禁用,1:正常)
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
        return '{{%addon_tiny_shop_product_attribute_value}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'product_id', 'type', 'sort', 'status', 'created_at', 'updated_at'], 'integer'],
            [['product_id'], 'required'],
            [['title'], 'string', 'max' => 125],
            [['value', 'data'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'merchant_id' => '商户id',
            'product_id' => '商品编码',
            'title' => '参数名称',
            'value' => '参数值',
            'type' => '属性对应输入类型1.直接2.单选3.多选',
            'sort' => '排序',
            'data' => 'Data',
            'status' => '状态(-1:已删除,0:禁用,1:正常)',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }
}
