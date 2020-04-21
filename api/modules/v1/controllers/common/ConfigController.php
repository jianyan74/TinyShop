<?php

namespace addons\TinyShop\api\modules\v1\controllers\common;

use Yii;
use api\controllers\OnAuthController;
use common\helpers\AddonHelper;

/**
 * 公用配置
 *
 * Class ConfigController
 * @package addons\TinyShop\api\modules\v1\controllers\common
 * @author jianyan74 <751393839@qq.com>
 */
class ConfigController extends OnAuthController
{
    public $modelClass = '';

    /**
     * 不用进行登录验证的方法
     *
     * 例如： ['index', 'update', 'create', 'view', 'delete']
     * 默认全部需要验证
     *
     * @var array
     */
    protected $authOptional = ['index'];

    /**
     * @return array|\yii\data\ActiveDataProvider
     */
    public function actionIndex()
    {
        $field = Yii::$app->request->get('field');
        $field = explode(',', $field);
        $field = array_intersect($field, array_keys([
            'title' => '商城名称',
            'web_url' => '官方网址',
            'key_words' => '商城关键字',
            'web_desc' => '商城描述',
            'web_logo' => '商城logo',
            'web_qrcode' => '商城二维码',
            'web_site_icp' => '备案号',
            'web_wechat_qrcode' => '商城公众号二维码',
            'web_phone' => '商城联系方式',
            'web_email' => '商城邮箱',
            'web_qq' => '商城QQ号',
            'web_weixin' => '商城微信号',
            'web_address' => '联系地址',
            'shouhou_date' => '售后设置(天)',
            'copyright_logo' => '版权logo',
            'copyright_companyname' => '公司名称',
            'copyright_url' => '版权链接',
            'copyright_desc' => '版权信息',
            'share_title' => '分享标题',
            'share_cover' => '分享封面',
            'share_desc' => '分享描述',
            'share_link' => '分享链接',
            // 协议使用
            'protocol_register' => '注册协议',
            'protocol_privacy' => '隐私协议',
            'protocol_recharge' => '充值协议',
            // 支付使用
            'order_balance_pay' => '是否开启余额支付',
            'order_wechat_pay' => '是否开启微信支付',
            'order_ali_pay' => '是否开启支付宝支付',
            // 商家地址(退货使用)
            'merchant_mobile' => '联系方式',
            'merchant_address' => '收货地址',
            'merchant_name' => '收件人',
            'merchant_zip_code' => '邮编',
            'merchant_longitude_latitude' => '地址经纬度',
            // 热门
            'hot_search_default' => '默认搜索',
            'hot_search_list' => '热门搜索',
            // 发票
            'order_invoice_tax' => '开票税额',
            'order_invoice_content' => '开票内容',
        ]));

        $result = [];
        $allConfig = AddonHelper::getConfig();
        foreach ($field as $item) {
            $result[$item] = $allConfig[$item] ?? '';
        }

        return $result;
    }
}