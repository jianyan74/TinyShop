<?php

namespace addons\TinyShop\common\forms;

use addons\TinyShop\common\enums\product\PosteCoverTypeEnum;
use addons\TinyShop\common\enums\product\PosteQrTypeEnum;
use yii\base\Model;

/**
 * Class SettingForm
 * @package addons\TinyShop\common\forms
 * @author jianyan74 <751393839@qq.com>
 */
class SettingForm extends Model
{
    /***************** 订单配置 *****************/
    public $order_auto_delivery = 14;
    public $order_buy_close_time = 60;
    public $order_not_pay_remind = 0;
    public $order_auto_complete_time = 0;
    public $order_min_pay_money = 0;
    public $order_evaluate = '客户默认好评~';
    public $order_evaluate_day = 30;
    public $order_after_sale_date = 0;
    public $order_oversold = 1;

    /***************** 支付配置 *****************/
    public $order_balance_pay = 1;
    public $order_wechat_pay = 0;
    public $order_ali_pay = 0;
    public $order_bytedance_pay = 0;
    public $order_cash_against_pay = 0;

    /***************** 商品设置 *****************/
    public $product_audit_status = 0;

    /***************** 订单发票 *****************/
    public $order_invoice_status = 0;
    public $order_invoice_tax = 20;
    public $order_invoice_content;

    /***************** 物流配送 *****************/
    public $logistics = 1;
    public $logistics_select = 0;
    public $logistics_local_distribution = 0;
    public $logistics_pick_up = 0;
    public $logistics_local_distribution_type = 1;

    /***************** 应用配置 *****************/
    public $app_name;
    public $app_logo;
    public $app_h5_url;

    /***************** 会员注册绑定 *****************/
    public $member_third_party_binding_type = 0;
    public $member_mobile_login_be_register = 0;
    public $member_mini_program_register_get_mobile = 0;
    public $member_login = 1;
    public $member_login_weight = 'account';
    public $member_third_party_login = 0;
    public $member_register = 1;
    public $member_register_promoter_code = 0;
    public $member_agreement_default_select = 0;

    /***************** 分享配置 *****************/
    public $share_title;
    public $share_cover;
    public $share_desc;
    public $share_link;

    /***************** 砍价 *****************/
    public $bargain = 1;
    public $bargain_max_num = 1;
    public $bargain_day_max_num = 3;
    public $bargain_binding_time = 0;
    public $bargain_binding_time_num = 0;
    public $bargain_promote_content;
    public $bargain_hint;
    public $bargain_first_hint;
    public $bargain_rule;

    /***************** 显示开关 *****************/
    public $member_recharge = 1;
    public $index_cate = 1;
    public $index_decoration = 1;
    public $store_entrance = 1;
    public $address_select_type = 1;

    // App
    public $app_service_app_type = 0;

    /***************** 系统维护 *****************/
    public $site_status = 1;
    public $site_close_time;
    public $site_close_explain;


    /***************** 商品二维码 *****************/
    public $product_poster_cover_type = PosteCoverTypeEnum::ROUNDNESS;
    public $product_poster_qr_type = PosteQrTypeEnum::COMMON_QR;
    public $product_poster_title = '为你挑选了一个好物';

    public function rules()
    {
        return [
            [
                [
                    'order_auto_delivery',
                    'order_buy_close_time',
                    'order_auto_complete_time',
                    'order_min_pay_money',
                    'order_evaluate',
                    'order_evaluate_day',
                    'order_after_sale_date',
                    'order_not_pay_remind',
                    'order_oversold',
                ],
                'required'
            ],
            [
                [
                    'order_auto_delivery',
                    'order_buy_close_time',
                    'order_auto_complete_time',
                    'order_min_pay_money',
                    'order_evaluate_day',
                    'order_after_sale_date',
                    'order_not_pay_remind',
                    'order_oversold',
                ],
                'integer',
                'min' => 0,
            ],
            [['order_buy_close_time'], 'integer', 'min' => 1],
            [['order_evaluate'], 'string'],
            [
                [
                    'order_balance_pay',
                    'order_wechat_pay',
                    'order_ali_pay',
                    'order_min_pay_money',
                    'order_bytedance_pay',
                    'order_cash_against_pay',
                ],
                'integer',
                'min' => 0,
            ],
            // 支付设置
            [
                [
                    'order_balance_pay',
                    'order_wechat_pay',
                    'order_ali_pay',
                    'order_bytedance_pay',
                    'order_cash_against_pay'
                ],
                'integer',
            ],
            // 商品设置
            [
                [
                    'product_audit_status',
                ],
                'integer',
            ],
            // 配送配置
            [
                [
                    'logistics',
                    'logistics_select',
                    'logistics_pick_up',
                    'logistics_local_distribution',
                    'logistics_local_distribution_type'
                ],
                'integer',
            ],
            // 发票配置
            [['order_invoice_status'], 'integer'],
            [['order_invoice_tax'], 'number', 'min' => 0, 'max' => 100],
            [['order_invoice_content'], 'string'],
            // 会员注册
            [
                [
                    'member_third_party_login',
                    'member_login',
                    'member_register',
                    'member_register_promoter_code',
                    'member_third_party_binding_type',
                    'member_mobile_login_be_register',
                    'member_mini_program_register_get_mobile',
                    'member_agreement_default_select',
                ],
                'integer',
            ],
            [['member_login_weight'], 'string'],
            // 显示开关
            [
                [
                    'store_entrance',
                    'member_recharge',
                    'index_cate',
                    'index_decoration',
                    'address_select_type',
                ],
                'integer',
            ],
            // 应用配置
            ['app_h5_url', 'url'],
            [['app_name', 'app_logo'], 'string'],
            // 分享配置
            [['share_title', 'share_cover', 'share_desc', 'share_link'], 'string', 'max' => 200],
            // 系统维护
            ['site_status', 'integer'],
            [
                ['site_close_explain'],
                'string',
                'max' => 200,
            ],
            // 商品二维码
            [['product_poster_title'], 'required'],
            [['product_poster_title', 'product_poster_cover_type'], 'string', 'max' => 20],
            [['product_poster_qr_type'], 'string', 'max' => 30],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'order_auto_delivery' => '发货后自动收货时间(天)',
            'order_buy_close_time' => '未付款自动关闭时间(分钟)',
            'order_auto_complete_time' => '收货后自动完成时间(天)',
            'order_min_pay_money' => '订单下单最低支付金额',
            'order_not_pay_remind' => '待付款订单催付弹窗',
            'order_oversold' => '超卖订单处理',
            'order_evaluate_day' => '系统默认评价时间(天)',
            'order_after_sale_date' => '完成后可维权时间(天)',
            'order_evaluate' => '默认评价语',
            // 支付配置
            'order_balance_pay' => '余额支付',
            'order_wechat_pay' => '微信支付',
            'order_ali_pay' => '支付宝支付',
            'order_bytedance_pay' => '字节跳动支付',
            'order_cash_against_pay' => '货到付款支付',
            // 商品设置
            'product_audit_status' => '商品审核',
            // 配送设置
            'logistics' => '物流配送',
            'logistics_select' => '选择物流',
            'logistics_cash_delivery' => '货到付款',
            'logistics_local_distribution' => '同城配送',
            'logistics_pick_up' => '买家自提',
            'logistics_local_distribution_type' => '同城配送方式',
            // 发票设置
            'order_invoice_status' => '发票',
            'order_invoice_tax' => '发票税率',
            'order_invoice_content' => '发票内容',
            // 会员注册
            'member_third_party_binding_type' => '第三方注册绑定设置',
            'member_mini_program_register_get_mobile' => '微信小程序获取手机号码',
            'member_mobile_login_be_register' => '手机验证码登录即注册',
            'member_login_weight' => '默认登录跳转页面',
            'member_login' => '账号密码登录',
            'member_register' => '会员注册',
            'member_third_party_login' => '第三方授权登录',
            'member_register_promoter_code' => '会员注册激活码填写',
            'member_agreement_default_select' => '注册登录协议默认选中',
            // 分享配置
            'share_title' => '分享标题',
            'share_cover' => '分享封面',
            'share_desc' => '分享描述',
            'share_link' => '分享链接',
            // 应用配置
            'app_name' => '应用名称',
            'app_logo' => '应用 logo',
            'app_h5_url' => '应用 H5 域名',
            // 显示开关
            'member_recharge' => '充值入口',
            'index_cate' => '首页顶部分类',
            'index_decoration' => '首页自定义装修',
            'store_entrance' => '店铺入口',
            'address_select_type' => '收货地址省市区选择类型',
        ];
    }

    /**
     * @return array
     */
    public function attributeHints()
    {
        return [
            // 订单设置
            'order_auto_delivery' => '订单多长时间后自动收货，单位为/天 (注：若为0，则订单不会自动收货)',
            'order_min_pay_money' => '订单实际支付金额低于该金额则不允许下单，单位：元',
            'order_buy_close_time' => '订单开始后多长时间未付款自动关闭，单位为/分钟',
            'order_auto_complete_time' => '收货后，多少时间订单自动完成，单位为/天',
            'order_after_sale_date' => '订单完成后，多长时间内可申请维权，设置为0则订单完成后不可维权',
            'order_evaluate_day' => '订单完成达到设置天数后，用户仍未进行评价，则系统进行默认评价',
            'order_oversold' => '出现超卖订单时，订单管理中会标记出此笔订单，可人工选择发货或主动退款关单。也可以选择系统自动退款，需配置退款证书',
            // 发票设置
            'order_invoice_tax' => '设置开发票的税率，单位为%',
            'order_invoice_content' => '客户要求开发票时可以选择的内容，逗号分格代表一个选项，例如：办公用品,明细',
            // 会员注册
            'member_mini_program_register_get_mobile' => '授权登录后如果发现没有手机号码会强制要求绑定',
            'member_mobile_login_be_register' => '请求手机登录后如果发现用户未注册会直接注册',
           // 应用
            'app_h5_url' => '用于后台的商品预览和自定义装修预览',
            // 同城配送
            'address_select_type' => '如果选择配送是同城配送需要开启"地图选点"且地址需要重新添加',
        ];
    }
}
