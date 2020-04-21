<?php

namespace addons\TinyShop\common\models;

use yii\base\Model;

/**
 * Class SettingForm
 * @package addons\TinyShop\common\models
 */
class SettingForm extends Model
{
    public $order_auto_delinery = 14;
    public $order_buy_close_time = 60;
    public $order_delivery_complete_time = 7;
    public $shopping_back_points = 3;
    public $evaluate_day;
    public $after_sale_date;
    public $evaluate;

    public $order_invoice_tax = 20;
    public $order_invoice_content;

    public $copyright_logo;
    public $copyright_companyname;
    public $copyright_url;
    public $copyright_desc;

    public $share_title;
    public $share_cover;
    public $share_desc;
    public $share_link;

    public $is_logistics = 0;
    public $buyer_self_lifting = 0;
    public $is_delivery_shop = 0;

    public $order_balance_pay = 1;
    public $order_wechat_pay = 0;
    public $order_ali_pay = 0;
    public $order_cash_against_pay = 0;

    public $protocol_register;
    public $protocol_privacy;
    public $protocol_recharge;

    public $is_open_commission = 0;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'buyer_self_lifting',
                    'order_auto_delinery',
                    'order_buy_close_time',
                    'order_balance_pay',
                    'order_wechat_pay',
                    'order_ali_pay',
                    'order_cash_against_pay',
                    'shopping_back_points',
                    'after_sale_date',
                    'evaluate_day',
                    'order_delivery_complete_time',
                    'is_logistics',
                    'is_delivery_shop',
                    'is_open_commission',
                ],
                'integer',
                'min' => 0,
            ],
            [['share_title', 'share_cover', 'copyright_companyname'], 'string', 'max' => 100],
            [['share_link', 'share_desc', 'evaluate', 'copyright_logo',], 'string', 'max' => 200],
            [['share_link', 'copyright_url'], 'url'],
            [['order_invoice_tax'], 'number', 'min' => 1, 'max' => 100],
            [['order_invoice_content', 'copyright_desc'], 'string', 'max' => 500],
            [['protocol_register', 'protocol_privacy', 'protocol_recharge'], 'string']
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'order_auto_delinery' => '自动收货时间(天)',
            'order_buy_close_time' => '订单自动关闭时间(分钟)',
            'order_balance_pay' => '开启余额支付',
            'order_wechat_pay' => '开启微信支付',
            'order_ali_pay' => '开启支付宝支付',
            'order_cash_against_pay' => '开启货到付款支付',
            'order_delivery_complete_time' => '订单完成时间(天)',
            'shopping_back_points' => '购物返积分设置',
            'evaluate_day' => '系统默认评价时间(天)',
            'after_sale_date' => '售后设置(天)',
            'evaluate' => '默认评价语',
            'copyright_logo' => '版权logo',
            'copyright_companyname' => '公司名称',
            'copyright_url' => '版权链接',
            'copyright_desc' => '版权信息',
            'order_invoice_tax' => '发票税率',
            'order_invoice_content' => '发票内容',
            'share_title' => '分享标题',
            'share_cover' => '分享封面',
            'share_desc' => '分享描述',
            'share_link' => '分享链接',
            'is_logistics' => '允许选择物流',
            'buyer_self_lifting' => '开启买家自提',
            'is_delivery_shop' => '开启本地配送',
            'protocol_register' => '注册协议',
            'protocol_privacy' => '隐私协议',
            'protocol_recharge' => '充值协议',
            'is_open_commission' => '开启分销',
        ];
    }

    /**
     * @return array
     */
    public function attributeHints()
    {
        return [
            'copyright_logo' => '建议使用宽280像素-高50像素内的GIF或PNG透明图片',
            'order_auto_delinery' => '订单多长时间后自动收货，单位为/天(注：若为0，则订单不会自动收货)',
            'order_buy_close_time' => '订单开始后多长时间未付款自动关闭，单位为/分钟(注：不填写或0订单将不会自动关闭)',
            'order_delivery_complete_time' => '收货后，多少时间订单自动完成，单位为/天',
            'evaluate_day' => '订单完成达到设置天数后，用户仍未进行评价，则系统进行默认评价',
            'after_sale_date' => '订单完成多少天之内可以售后, 不填默认不限',
            'shopping_back_points' => '在什么时间将购物返积分添加到会员账户',
            'order_invoice_tax' => '设置开发票的税率，单位为%',
            'order_invoice_content' => '客户要求开发票时可以选择的内容，逗号分格代表一个选项，例如：办公用品,明细',
        ];
    }
}