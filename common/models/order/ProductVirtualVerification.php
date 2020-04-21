<?php

namespace addons\TinyShop\common\models\order;

use common\traits\HasOneMerchant;

/**
 * This is the model class for table "{{%addon_shop_order_product_virtual_verification}}".
 *
 * @property int $id
 * @property int $merchant_id 商户id
 * @property int $member_id 商品所有人
 * @property string $merchant_name 虚拟商品所有者
 * @property int $product_virtual_id 用户虚拟商品id
 * @property int $product_virtual_state 用户虚拟商品使用状态
 * @property string $action 动作内容
 * @property int $num 核销次数
 * @property int $product_id 商品id
 * @property string $product_name 虚拟商品名称
 * @property int $auditor_id 核销人员id
 * @property string $auditor_name 核销员
 * @property int $created_at 创建时间
 * @property int $updated_at 修改时间
 */
class ProductVirtualVerification extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_order_product_virtual_verification}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'member_id', 'product_virtual_id', 'product_virtual_state', 'num', 'product_id', 'auditor_id', 'created_at', 'updated_at'], 'integer'],
            [['merchant_name', 'product_name', 'auditor_name'], 'string', 'max' => 50],
            [['action'], 'string', 'max' => 255],
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
            'member_id' => '商品所有人',
            'merchant_name' => '虚拟商品所有者',
            'product_virtual_id' => '用户虚拟商品id',
            'product_virtual_state' => '用户虚拟商品使用状态',
            'action' => '动作内容',
            'num' => '核销次数',
            'product_id' => '商品id',
            'product_name' => '虚拟商品名称',
            'auditor_id' => '核销人员id',
            'auditor_name' => '核销员',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }
}
