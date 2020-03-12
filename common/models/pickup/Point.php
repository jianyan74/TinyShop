<?php

namespace addons\TinyShop\common\models\pickup;

use Yii;
use common\behaviors\MerchantBehavior;

/**
 * This is the model class for table "{{%addon_shop_pickup_point}}".
 *
 * @property string $id
 * @property int $merchant_id 店铺ID
 * @property string $name 自提点名称
 * @property string $address 自提点地址
 * @property string $contact 联系人
 * @property string $mobile 联系电话
 * @property int $city_id 市ID
 * @property int $province_id 省ID
 * @property int $area_id 区县ID
 * @property string $lng 经度
 * @property string $lat 维度
 * @property int $status 状态
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class Point extends \common\models\base\BaseModel
{
    use MerchantBehavior;

    public $longitude_latitude;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_pickup_point}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'sort', 'city_id', 'province_id', 'area_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['name', 'city_id', 'province_id', 'area_id', 'address', 'mobile'], 'required'],
            [['name'], 'string', 'max' => 150],
            [['address', 'address_name'], 'string', 'max' => 200],
            [['contact'], 'string', 'max' => 100],
            [['mobile', 'lng', 'lat'], 'string', 'max' => 50],
            [['longitude_latitude'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'merchant_id' => 'Merchant ID',
            'name' => '门店名称',
            'address' => '详细地址',
            'contact' => '联系人',
            'mobile' => '联系电话',
            'city_id' => '市',
            'province_id' => '省',
            'area_id' => '区',
            'lng' => '经度',
            'lat' => '维度',
            'sort' => '排序',
            'status' => '状态',
            'longitude_latitude' => '经纬度选择',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function afterFind()
    {
        if (!empty($this->lng) && !empty($this->lat) ) {
            $this->longitude_latitude['lng'] = $this->lng;
            $this->longitude_latitude['lat'] = $this->lat;
        }

        parent::afterFind();
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        $this->address_name = Yii::$app->services->provinces->getCityListName([$this->province_id, $this->city_id, $this->area_id]);
        if (!empty($this->longitude_latitude)) {
            $this->lng = $this->longitude_latitude['lng'];
            $this->lat = $this->longitude_latitude['lat'];
        }

        return parent::beforeSave($insert);
    }

    /**
     * @return bool
     */
    public function beforeDelete()
    {
        Auditor::deleteAll(['pickup_point_id' => $this->id]);

        return parent::beforeDelete();
    }
}
