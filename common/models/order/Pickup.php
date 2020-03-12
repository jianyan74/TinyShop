<?php

namespace addons\TinyShop\common\models\order;

/**
 * This is the model class for table "{{%addon_shop_order_pickup}}".
 *
 * @property string $id
 * @property int $order_id 订单ID
 * @property int $merchant_id 商户id
 * @property string $name 自提点名称
 * @property string $address 自提点地址
 * @property string $contact 联系人
 * @property string $mobile 联系电话
 * @property int $city_id 市ID
 * @property int $province_id 省ID
 * @property int $area_id 区县ID
 * @property string $lng 经度
 * @property string $lat 维度
 * @property string $buyer_name 提货人姓名
 * @property string $buyer_mobile 提货人电话
 * @property string $remark 提货备注信息
 * @property string $pickup_code 自提码
 * @property int $pickup_time 自提时间
 * @property int $pickup_status 自提状态 0未自提 1已提货
 * @property int $auditor_id 审核人id
 * @property int $pickup_id 自提点门店id
 * @property int $status 状态
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class Pickup extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_order_pickup}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['member_id', 'order_id', 'merchant_id', 'city_id', 'province_id', 'area_id', 'pickup_time', 'pickup_status', 'auditor_id', 'pickup_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['name', 'city_id', 'province_id', 'area_id', 'address', 'mobile'], 'required'],
            [['name'], 'string', 'max' => 150],
            [['address', 'remark'], 'string', 'max' => 200],
            [['contact'], 'string', 'max' => 100],
            [['mobile', 'lng', 'lat', 'buyer_name', 'buyer_mobile', 'pickup_code'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'merchant_id' => 'Merchant ID',
            'member_id' => '用户',
            'name' => '自提点名称',
            'address' => '自提点地址',
            'contact' => '联系人',
            'mobile' => '联系电话',
            'city_id' => '市',
            'province_id' => '省',
            'area_id' => '区',
            'lng' => '经度',
            'lat' => '维度',
            'buyer_name' => '提货人姓名',
            'buyer_mobile' => '提货人电话',
            'remark' => '备注',
            'pickup_code' => '自提码',
            'pickup_time' => '自提时间',
            'pickup_status' => '自提状态',
            'auditor_id' => '审核人',
            'pickup_id' => '自提点门店',
            'status' => '状态',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
