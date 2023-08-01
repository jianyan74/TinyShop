<?php

namespace addons\TinyShop\common\models\product;

use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%addon_tiny_shop_product_tag}}".
 *
 * @property int $id
 * @property int|null $merchant_id 商户id
 * @property string $title 标签名称
 * @property int $sort 排列
 * @property int $status 状态
 * @property int|null $created_at 创建时间
 * @property int|null $updated_at 修改时间
 */
class Tag extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_product_tag}}';
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
            'id' => 'ID',
            'merchant_id' => '商户id',
            'title' => '标签名称',
            'sort' => '排列',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }
}
