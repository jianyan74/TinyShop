<?php

namespace addons\TinyShop\common\forms;

use yii\base\Model;

/**
 * Class OrderSearchForm
 * @package addons\TinyShop\common\forms
 */
class OrderSearchForm extends Model
{
    public $synthesize_status = '';
    public $order_type = '';
    public $start_time = '';
    public $end_time = '';
    public $order_sn;
    public $member_id;
    public $caballero_member_id;
    public $shipping_type;
    public $keyword;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['order_type', 'order_sn', 'keyword'], 'string'],
            [['member_id', 'caballero_member_id', 'shipping_type', 'start_time', 'end_time', 'synthesize_status'], 'integer'],
        ];
    }
}