<?php

namespace addons\TinyShop\common\models\order;

use Yii;

/**
 * This is the model class for table "{{%addon_tiny_shop_order_store}}".
 *
 * @property int $id
 * @property int|null $merchant_id 店铺ID
 * @property int|null $member_id 用户id
 * @property int|null $order_id 订单ID
 * @property string|null $title 自提点名称
 * @property string|null $cover 封面
 * @property string|null $contacts 联系人
 * @property string|null $mobile 联系电话
 * @property string|null $tel_no 电话号码
 * @property int|null $province_id 省ID
 * @property int|null $city_id 市ID
 * @property int|null $area_id 区县ID
 * @property string|null $address_name 地址
 * @property string|null $address_details 详细地址
 * @property string|null $longitude 经度
 * @property string|null $latitude 纬度
 * @property string|null $buyer_name 提货人姓名
 * @property string|null $buyer_mobile 提货人电话
 * @property string|null $remark 提货备注信息
 * @property string|null $pickup_code 自提码
 * @property int|null $pickup_time 自提时间
 * @property int|null $pickup_status 自提状态 0未自提 1已提货
 * @property int|null $store_id 自提点门店id
 * @property int|null $auditor_id 审核人id
 * @property int|null $status 状态
 * @property int|null $updated_at 更新时间
 * @property int|null $created_at 创建时间
 */
class Store extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_order_store}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'member_id', 'order_id', 'province_id', 'city_id', 'area_id', 'pickup_time', 'pickup_status', 'store_id', 'auditor_id', 'status', 'updated_at', 'created_at'], 'integer'],
            [['title'], 'string', 'max' => 150],
            [['cover'], 'string', 'max' => 255],
            [['contacts', 'address_details', 'longitude', 'latitude'], 'string', 'max' => 100],
            [['mobile', 'buyer_name', 'pickup_code'], 'string', 'max' => 50],
            [['tel_no'], 'string', 'max' => 20],
            [['address_name', 'remark'], 'string', 'max' => 200],
            [['buyer_mobile'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'merchant_id' => '店铺ID',
            'member_id' => '用户id',
            'order_id' => '订单ID',
            'title' => '自提点名称',
            'cover' => '封面',
            'contacts' => '联系人',
            'mobile' => '联系电话',
            'tel_no' => '电话号码',
            'province_id' => '省ID',
            'city_id' => '市ID',
            'area_id' => '区县ID',
            'address_name' => '地址',
            'address_details' => '详细地址',
            'longitude' => '经度',
            'latitude' => '纬度',
            'buyer_name' => '提货人姓名',
            'buyer_mobile' => '提货人电话',
            'remark' => '提货备注信息',
            'pickup_code' => '自提码',
            'pickup_time' => '自提时间',
            'pickup_status' => '自提状态 0未自提 1已提货',
            'store_id' => '自提点门店id',
            'auditor_id' => '审核人id',
            'status' => '状态',
            'updated_at' => '更新时间',
            'created_at' => '创建时间',
        ];
    }
}
