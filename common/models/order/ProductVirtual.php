<?php

namespace addons\TinyShop\common\models\order;

use common\traits\HasOneMerchant;

/**
 * This is the model class for table "{{%addon_shop_order_product_virtual}}".
 *
 * @property int $id 主键id
 * @property int $merchant_id 商户id
 * @property int $sku_id 规格id
 * @property int $product_id 商品id
 * @property string $product_name 虚拟商品名称
 * @property string $product_group 商品类型
 * @property resource $code 虚拟码
 * @property string $money 虚拟商品金额
 * @property int $member_id 买家id
 * @property string $member_nickname 买家名称
 * @property int $order_product_id 关联订单项id
 * @property string $order_sn 订单编号
 * @property int $period 有效期/天(0表示不限制)
 * @property int $start_time 有效期开始时间
 * @property int $end_time 有效期结束时间
 * @property int $use_number 使用次数
 * @property int $confine_use_number 限制使用次数
 * @property string $remark 备注
 * @property int $state 使用状态(-1:已过期,0:未使用,1:已使用)
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class ProductVirtual extends \common\models\base\BaseModel
{
    use HasOneMerchant;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_order_product_virtual}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'sku_id', 'product_id', 'member_id', 'order_product_id', 'period', 'start_time', 'end_time', 'use_number', 'confine_use_number', 'state', 'created_at', 'updated_at'], 'integer'],
            [['money'], 'number'],
            [['product_name', 'code', 'member_nickname', 'order_sn', 'remark'], 'string', 'max' => 255],
            [['product_group'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键id',
            'merchant_id' => '商户id',
            'sku_id' => '规格id',
            'product_id' => '商品id',
            'product_name' => '虚拟商品名称',
            'product_group' => '商品类型',
            'code' => '虚拟码',
            'money' => '虚拟商品金额',
            'member_id' => '买家id',
            'member_nickname' => '买家名称',
            'order_product_id' => '关联订单项id',
            'order_sn' => '订单编号',
            'period' => '有效期/天(0表示不限制)',
            'start_time' => '有效期开始时间',
            'end_time' => '有效期结束时间',
            'use_number' => '使用次数',
            'confine_use_number' => '限制使用次数',
            'remark' => '备注',
            'state' => '使用状态(-2:待发放-1:已过期,0:未使用,1:已使用)',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::class, ['order_sn' => 'order_sn']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderProduct()
    {
        return $this->hasOne(OrderProduct::class, ['id' => 'order_product_id']);
    }
}
