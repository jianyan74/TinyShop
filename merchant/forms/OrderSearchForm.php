<?php

namespace addons\TinyShop\merchant\forms;

use yii\base\Model;

/**
 * Class OrderSearchForm
 * @package addons\TinyShop\merchant\forms
 * @author jianyan74 <751393839@qq.com>
 */
class OrderSearchForm extends Model
{
    public $start_time;
    public $end_time;
    public $query_type;
    public $keyword;
    public $order_status;
    public $payment_type;
    public $order_from;
    public $order_type;
    public $shipping_type;

    public function rules()
    {
        return [
            [['order_status', 'payment_type', 'query_type', 'order_type', 'shipping_type'], 'integer'],
            [['keyword', 'start_time', 'end_time', 'order_from'], 'string'],

        ];
    }

    /**
     * @return array
     */
    public function getKeyword()
    {
        if ($this->keyword) {
            switch ($this->query_type) {
                // 订单编号
                case 1 :
                    return ['like', 'o.order_sn', $this->keyword];
                    break;
                // 订单交易号
                case 2 :
                    return ['like', 'o.out_trade_no', $this->keyword];
                    break;
                // 收货人姓名
                case 3 :
                    return ['like', 'o.receiver_name', $this->keyword];
                    break;
                // 收货人手机
                case 4 :
                    return ['like', 'o.receiver_mobile', $this->keyword];
                    break;
            }
        }

        return [];
    }

    /**
     * @return array
     */
    public function getBetweenTime()
    {
        if ($this->start_time && $this->end_time) {
            return ['between', 'o.created_at', strtotime($this->start_time), strtotime($this->end_time)];
        }

        return [];
    }
}