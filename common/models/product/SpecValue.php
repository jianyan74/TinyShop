<?php

namespace addons\TinyShop\common\models\product;

use Yii;

/**
 * This is the model class for table "{{%addon_tiny_shop_product_spec_value}}".
 *
 * @property int $id
 * @property int|null $merchant_id 商户id
 * @property int|null $product_id 商品编码
 * @property string|null $title 属性标题
 * @property int|null $common_spec_id 系统规格id
 * @property int|null $common_spec_value_id 系统规格值id
 * @property string|null $data 属性值例如颜色
 * @property int|null $sort 排序
 * @property int|null $pitch_on 选中
 * @property int|null $status 状态(-1:已删除,0:禁用,1:正常)
 * @property int|null $created_at 创建时间
 * @property int|null $updated_at 修改时间
 */
class SpecValue extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_product_spec_value}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'product_id', 'common_spec_id', 'common_spec_value_id', 'sort', 'pitch_on', 'status', 'created_at', 'updated_at'], 'integer'],
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
            'merchant_id' => '商户id',
            'product_id' => '商品编码',
            'title' => '属性标题',
            'common_spec_id' => '系统规格id',
            'common_spec_value_id' => '系统规格值id',
            'data' => '属性值例如颜色',
            'sort' => '排序',
            'pitch_on' => '选中',
            'status' => '状态(-1:已删除,0:禁用,1:正常)',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    public function getSpec()
    {
        return $this->hasOne(Spec::class, ['common_spec_id' => 'common_spec_id']);
    }
}
