<?php

namespace addons\TinyShop\merchant\forms;

use yii\base\Model;

/**
 * Class PickupPointConfigForm
 * @package addons\TinyShop\merchant\forms
 * @author jianyan74 <751393839@qq.com>
 */
class PickupPointConfigForm extends Model
{
    public $pickup_point_fee = 0;
    public $pickup_point_freight = 0;
    public $pickup_point_is_open = 0;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pickup_point_fee', 'pickup_point_freight', 'pickup_point_is_open'], 'number', 'min' => 0],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'pickup_point_fee' => '门店运费',
            'pickup_point_freight' => '满X免运费',
            'pickup_point_is_open' => '是否启用',
        ];
    }

    /**
     * @return array
     */
    public function attributeHints()
    {
        return [
            'pickup_point_fee' => '如果会员选择配送方式是门店自提对应运费',
        ];
    }
}