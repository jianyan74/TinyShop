<?php
namespace addons\TinyShop\common\models\product;

use common\behaviors\MerchantBehavior;
use Yii;

/**
 * This is the model class for table "{{%addon_shop_product_spec}}".
 *
 * @property string $id
 * @property string $merchant_id 商户id
 * @property int $product_id 商品编码
 * @property int $base_spec_id 系统规格id
 * @property string $title 规格名称
 * @property int $sort 排序
 * @property int $show_type 展示方式 1 文字 2 颜色 3 图片
 * @property int $status 状态(-1:已删除,0:禁用,1:正常)
 * @property string $created_at 创建时间
 * @property string $updated_at 修改时间
 */
class Spec extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_product_spec}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'product_id', 'base_spec_id', 'sort', 'show_type', 'status', 'created_at', 'updated_at'], 'integer'],
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
            'merchant_id' => 'Merchant ID',
            'product_id' => 'Product ID',
            'base_spec_id' => 'Base Spec ID',
            'title' => 'Title',
            'sort' => 'Sort',
            'show_type' => 'Show Type',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * 关联规格值
     *
     * @return \yii\db\ActiveQuery
     */
    public function getValue()
    {
        return $this->hasMany(SpecValue::class, ['base_spec_id' => 'base_spec_id'])
            ->select(['base_spec_id', 'base_spec_value_id', 'title', 'sort', 'data'])
            ->orderBy('sort asc');
    }
}
