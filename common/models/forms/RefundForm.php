<?php

namespace addons\TinyShop\common\models\forms;

use addons\TinyShop\common\models\order\OrderProduct;

/**
 * Class RefundForm
 * @package addons\TinyShop\common\models\forms
 * @author jianyan74 <751393839@qq.com>
 */
class RefundForm extends OrderProduct
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['refund_type', 'refund_reason'], 'required', 'on' => 'apply'],
            [['refund_shipping_code', 'refund_shipping_company'], 'required', 'on' => 'salesReturn'],
            [['refund_type'], 'integer'],
            [['refund_reason', 'refund_shipping_code', 'refund_shipping_company'], 'string', 'max' => 200],
        ];
    }

    /**
     * 场景
     *
     * @return array
     */
    public function scenarios()
    {
        return [
            'default' => array_keys($this->attributeLabels()),
            'apply' => array_keys($this->attributeLabels()),
            'salesReturn' => array_keys($this->attributeLabels()),
        ];
    }
}