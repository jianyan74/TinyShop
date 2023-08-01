<?php

namespace addons\TinyShop\merchant\modules\common\forms;

use common\helpers\ArrayHelper;
use addons\TinyShop\common\models\common\MerchantAddress;

/**
 * Class ReturnAddressForm
 * @package addons\TinyShop\merchant\modules\common\forms
 * @author jianyan74 <751393839@qq.com>
 */
class MerchantAddressForm extends MerchantAddress
{
    /**
     * @var array
     */
    public $longitude_latitude = [];

    public function afterFind()
    {
        $this->longitude_latitude = [
            'longitude' => !empty($this->longitude) ? $this->longitude : '116.39776',
            'latitude' => !empty($this->latitude) ? $this->latitude : '39.906777',
        ];

        parent::afterFind();
    }

    /**
     * @return array
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['longitude_latitude', 'safe'],
        ]);
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'longitude_latitude' => '地址经纬度',
        ]);
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (!empty($this->longitude_latitude)) {
            $this->longitude = $this->longitude_latitude['longitude'];
            $this->latitude = $this->longitude_latitude['latitude'];
        }

        return parent::beforeSave($insert);
    }
}
