<?php

namespace addons\TinyShop\common\models\product;

use Yii;

/**
 * This is the model class for table "{{%addon_tiny_shop_product_spec}}".
 *
 * @property int $id
 * @property int|null $merchant_id 商户id
 * @property int $product_id 商品编码
 * @property int $common_spec_id 系统规格id
 * @property string $title 规格名称
 * @property int $sort 排序
 * @property int|null $type 展示方式 1 文字 2 颜色 3 图片
 * @property int $status 状态(-1:已删除,0:禁用,1:正常)
 * @property int|null $created_at 创建时间
 * @property int|null $updated_at 修改时间
 */
class Spec extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_product_spec}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'product_id', 'common_spec_id', 'sort', 'type', 'status', 'created_at', 'updated_at'], 'integer'],
            [['product_id'], 'required'],
            [['title'], 'string', 'max' => 125],
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
            'common_spec_id' => '系统规格id',
            'title' => '规格名称',
            'sort' => '排序',
            'type' => '展示方式 1 文字 2 颜色 3 图片',
            'status' => '状态(-1:已删除,0:禁用,1:正常)',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getValue()
    {
        return $this->hasMany(SpecValue::class, ['product_id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getValueBySpec()
    {
        return $this->hasMany(SpecValue::class, ['common_spec_id' => 'common_spec_id']);
    }
}
