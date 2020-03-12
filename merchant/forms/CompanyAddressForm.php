<?php

namespace addons\TinyShop\merchant\forms;

use yii\base\Model;

/**
 * Class CompanyAddressForm
 * @package addons\TinyShop\merchant\forms
 * @author jianyan74 <751393839@qq.com>
 */
class CompanyAddressForm extends Model
{
    public $merchant_address;
    public $merchant_name;
    public $merchant_mobile;
    public $merchant_zip_code;
    public $merchant_longitude_latitude;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_name', 'merchant_address', 'merchant_mobile'], 'required'],
            [['merchant_name', 'merchant_zip_code'], 'string', 'max' => 100],
            [['merchant_address'], 'string', 'max' => 200],
            [['merchant_mobile'], 'string', 'max' => 50],
            [['merchant_longitude_latitude'], 'safe'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'merchant_mobile' => '联系方式',
            'merchant_address' => '收货地址',
            'merchant_name' => '收件人',
            'merchant_zip_code' => '邮编',
            'merchant_longitude_latitude' => '地址经纬度',
        ];
    }
}