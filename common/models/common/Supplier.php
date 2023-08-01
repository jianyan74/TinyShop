<?php

namespace addons\TinyShop\common\models\common;

use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%addon_tiny_shop_common_supplier}}".
 *
 * @property int $id
 * @property int|null $merchant_id 商户id
 * @property string $name 供货商名称
 * @property string $describe 供货商描述
 * @property int|null $sort 排序
 * @property int|null $province_id 省
 * @property int|null $city_id 城市
 * @property int|null $area_id 地区
 * @property string|null $address_name 地址
 * @property string|null $address_details 详细地址
 * @property string|null $longitude 经度
 * @property string|null $latitude 纬度
 * @property string|null $contacts 联系人
 * @property string|null $mobile 手机号码
 * @property string|null $tel_no 电话号码
 * @property int|null $status 状态
 * @property int|null $created_at 创建时间
 * @property int|null $updated_at 更新时间
 */
class Supplier extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_common_supplier}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'contacts', 'mobile'], 'required'],
            [['merchant_id', 'sort', 'province_id', 'city_id', 'area_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['describe'], 'string', 'max' => 1000],
            [['address_name'], 'string', 'max' => 200],
            [['address_details', 'longitude', 'latitude', 'contacts', 'mobile'], 'string', 'max' => 100],
            [['tel_no'], 'string', 'max' => 20],
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
            'name' => '供货商名称',
            'describe' => '供货商描述',
            'sort' => '排序',
            'province_id' => '省',
            'city_id' => '城市',
            'area_id' => '地区',
            'address_name' => '地址',
            'address_details' => '详细地址',
            'longitude' => '经度',
            'latitude' => '纬度',
            'contacts' => '联系人',
            'mobile' => '手机号码',
            'tel_no' => '电话号码',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
