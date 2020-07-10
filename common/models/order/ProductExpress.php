<?php

namespace addons\TinyShop\common\models\order;

use common\enums\StatusEnum;
use Yii;

/**
 * This is the model class for table "{{%addon_shop_order_product_express}}".
 *
 * @property string $id
 * @property int $order_id 订单id
 * @property string $express_name 包裹名称  （包裹- 1 包裹 - 2）
 * @property array $order_product_ids 产品id
 * @property int $shipping_type 发货方式1 需要物流 0无需物流
 * @property int $express_company_id 快递公司id
 * @property string $express_company 物流公司名称
 * @property string $express_no 运单编号
 * @property int $member_id 用户id
 * @property string $member_username 用户名
 * @property string $memo 备注
 * @property int $status 状态[-1:删除;0:禁用;1启用]
 * @property int $created_at
 * @property int $updated_at
 */
class ProductExpress extends \common\models\base\BaseModel
{
    const SHIPPING_TYPE_NOT_LOGISTICS = 0;
    const SHIPPING_TYPE_LOGISTICS = 1;

    /**
     * @var array
     */
    public static $shippingTypeExplain = [
        self::SHIPPING_TYPE_NOT_LOGISTICS => '无需物流',
        self::SHIPPING_TYPE_LOGISTICS => '需要物流',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_order_product_express}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'shipping_type', 'express_company_id', 'buyer_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['order_product_ids'], 'safe'],
            [['express_name', 'express_no', 'buyer_name'], 'string', 'max' => 50],
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
            'express_name' => 'Express Name',
            'order_product_ids' => 'Order Product Ids',
            'shipping_type' => '发货方式',
            'express_company_id' => '快递公司',
            'express_company' => '快递公司名称',
            'express_no' => '快递单号',
            'buyer_id' => '用户id',
            'buyer_name' => '用户名称',
            'memo' => '备注',
            'status' => '状态',
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
