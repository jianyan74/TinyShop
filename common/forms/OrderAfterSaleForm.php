<?php

namespace addons\TinyShop\common\forms;

use addons\TinyShop\common\models\order\AfterSale;

/**
 * Class OrderAfterSaleForm
 * @package addons\TinyShop\common\forms
 * @author jianyan74 <751393839@qq.com>
 */
class OrderAfterSaleForm extends AfterSale
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['refund_reason', 'refund_type'], 'required', 'on' => 'apply'],
            [['refund_pay_type', 'refund_money'], 'required', 'on' => 'affirmRefund'],
            [['member_express_no', 'member_express_company'], 'required', 'on' => 'salesReturn'],
            [['merchant_express_mobile', 'member_express_mobile'], 'string', 'max' => 20],
            [['refund_type'], 'integer'],
            [['refund_evidence'], 'safe'],
            [['refund_apply_money' ,'refund_money'], 'number', 'min' => 0],
            [['refund_reason', 'refund_explain', 'member_express_no', 'member_express_company'], 'string', 'max' => 200],
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
            'affirmRefund' => array_keys($this->attributeLabels()),
        ];
    }
}
