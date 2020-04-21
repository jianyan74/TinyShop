<?php

namespace addons\TinyShop\common\models\product;

use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%addon_shop_product_virtual_type}}".
 *
 * @property int $id 虚拟商品类型id
 * @property int $merchant_id 商户id
 * @property int $product_id 关联商品id
 * @property string $group 关联虚拟商品组别
 * @property int $period 有效期/天(0表示不限制)
 * @property int $confine_use_number 限制使用次数
 * @property array $value 值详情
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class VirtualType extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_product_virtual_type}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['period', 'confine_use_number'], 'required'],
            [['merchant_id', 'product_id', 'period', 'confine_use_number', 'created_at', 'updated_at'], 'integer'],
            [['value'], 'safe'],
            [['group'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '虚拟商品类型id',
            'merchant_id' => '商户id',
            'product_id' => '关联商品id',
            'group' => '关联虚拟商品组别',
            'period' => '有效期/天',
            'confine_use_number' => '限制使用次数',
            'value' => '值详情',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
