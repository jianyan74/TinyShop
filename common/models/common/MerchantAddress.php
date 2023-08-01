<?php

namespace addons\TinyShop\common\models\common;

use Yii;
use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%addon_tiny_shop_common_merchant_address}}".
 *
 * @property int $id
 * @property int|null $merchant_id 商户id
 * @property int|null $store_id 门店id
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
 * @property int|null $type 地址类型
 * @property int|null $status 状态[-1:删除;0:禁用;1启用]
 * @property int|null $created_at 创建时间
 * @property int|null $updated_at 更新时间
 */
class MerchantAddress extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_tiny_shop_common_merchant_address}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['province_id', 'city_id', 'area_id', 'address_details', 'contacts', 'mobile'], 'required'],
            [['merchant_id', 'store_id', 'sort', 'type', 'province_id', 'city_id', 'area_id', 'status', 'created_at', 'updated_at'], 'integer'],
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
            'store_id' => '门店id',
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
            'type' => '地址类型',
            'sort' => '排序',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    public function beforeSave($insert)
    {
        if ($this->area_id != $this->oldAttributes['area_id']) {
            $this->address_name = Yii::$app->services->provinces->getCityListName([$this->province_id, $this->city_id, $this->area_id]);
        }

        return parent::beforeSave($insert);
    }
}
