<?php

namespace addons\TinyShop\merchant\modules\marketing\forms;

use yii\base\Model;

/**
 * Class PointExchangeVerifyForm
 * @package addons\TinyShop\merchant\modules\marketing\forms
 * @author jianyan74 <751393839@qq.com>
 */
class PointExchangeVerifyForm extends Model
{
    public $max_buy;
    public $marketing_stock;
    public $point;
    public $discount;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['discount', 'point', 'max_buy', 'marketing_stock'], 'required'],
            [['max_buy', 'marketing_stock'], 'integer', 'min' => 0],
            [['discount'], 'number', 'min' => 0],
            [['point'], 'integer', 'min' => 1],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels()
    {
        return [
            'discount' => '金额',
            'point' => '积分',
            'marketing_stock' => '库存',
            'max_buy' => '限购',
        ];
    }
}
