<?php
namespace addons\TinyShop\common\models\product;

use Yii;

/**
 * This is the model class for table "{{%addon_shop_product_spec_value}}".
 *
 * @property string $id
 * @property int $product_id 商品编码
 * @property string $base_spec_id 系统规格id
 * @property string $base_spec_value_id 系统规格值id
 * @property string $title 属性标题
 * @property string $data 属性值例如颜色
 * @property int $sort 排序
 * @property int $status 状态(-1:已删除,0:禁用,1:正常)
 * @property string $created_at 创建时间
 * @property string $updated_at 修改时间
 */
class SpecValue extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_product_spec_value}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id'], 'required'],
            [['product_id', 'base_spec_id', 'base_spec_value_id', 'sort', 'status', 'created_at', 'updated_at'], 'integer'],
            [['title', 'data'], 'string', 'max' => 125],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'base_spec_id' => 'Base Spec ID',
            'base_spec_value_id' => 'Base Spec Value ID',
            'title' => 'Title',
            'data' => 'data',
            'sort' => 'Sort',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
