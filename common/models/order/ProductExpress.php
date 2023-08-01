<?php

namespace addons\TinyShop\common\models\order;

use common\enums\StatusEnum;

/**
 * This is the model class for table "{{%addon_tiny_shop_order_product_express}}".
 *
 * @property int $id
 * @property int $order_id 订单id
 * @property string|null $order_product_ids 商品id
 * @property string|null $name 包裹名称  （包裹- 1 包裹 - 2）
 * @property int|null $shipping_type 发货方式1 需要物流 0无需物流
 * @property int|null $express_company_id 快递公司id
 * @property string|null $express_company 物流公司名称
 * @property string|null $express_no 运单编号
 * @property int|null $buyer_id 买家id
 * @property string|null $buyer_realname 买家会员名称
 * @property string|null $buyer_mobile 买家会员手机号码
 * @property int|null $operator_id 发货人用户id
 * @property string|null $operator_username 发货人用户名
 * @property string|null $memo 备注
 * @property int|null $status 状态[-1:删除;0:禁用;1启用]
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class ProductExpress extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_order_product_express}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id'], 'required'],
            [['order_id', 'shipping_type', 'express_company_id', 'buyer_id', 'operator_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['order_product_ids'], 'safe'],
            [['name', 'express_no', 'buyer_realname', 'buyer_mobile', 'operator_username'], 'string', 'max' => 50],
            [['express_company', 'memo'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => '订单id',
            'order_product_ids' => '商品',
            'name' => '包裹名称',
            'shipping_type' => '发货方式',
            'express_company_id' => '快递公司',
            'express_company' => '物流公司名称',
            'express_no' => '快递单号',
            'buyer_id' => '买家id',
            'buyer_realname' => '买家会员名称',
            'buyer_mobile' => '买家会员手机号码',
            'operator_id' => '发货人用户id',
            'operator_username' => '发货人用户名',
            'memo' => '备注',
            'status' => '状态[-1:删除;0:禁用;1启用]',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if ($this->shipping_type == StatusEnum::DISABLED) {
            $this->express_company_id = 0;
            $this->express_company = '';
            $this->express_no = '';
        }

        return parent::beforeSave($insert);
    }
}
