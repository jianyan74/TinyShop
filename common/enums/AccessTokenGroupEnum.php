<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * Class AccessTokenGroupEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class AccessTokenGroupEnum extends BaseEnum
{
    /**
     * 组别 主要用于多端登录
     */
    const DEFAULT = 'tinyShop';
    const IOS = 'tinyShopIos'; // ios
    const ANDROID = 'tinyShopAndroid'; // 安卓
    const APP = 'tinyShopApp'; // app通用
    const H5 = 'tinyShopH5'; // H5
    const PC = 'tinyShopPc'; // Pc
    const WECHAT = 'tinyShopWechat'; // 微信小程序
    const WECHAT_MQ = 'tinyShopWechatMq'; // 微信小程序
    const ALI_MQ = 'tinyShopAliMq'; // 支付宝小程序
    const QQ_MQ = 'tinyShopQqMq'; // QQ小程序
    const BAIDU_MQ = 'tinyShopBaiduMq'; // 百度小程序
    const DING_TALK_MQ = 'tinyShopDingTalkMq'; // 钉钉小程序
    const TOU_TIAO_MQ = 'tinyShopTouTiaoMq'; // 头条小程序

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::DEFAULT => '默认',
            self::IOS => 'Ios',
            self::ANDROID => 'Android',
            self::APP => 'App',
            self::H5 => 'H5',
            self::PC => 'Pc',
            self::WECHAT => '微信',
            self::WECHAT_MQ => '微信小程序',
            self::ALI_MQ => '支付宝小程序',
            self::QQ_MQ => 'QQ小程序',
            self::BAIDU_MQ => '百度小程序',
            self::DING_TALK_MQ => '钉钉小程序',
            self::TOU_TIAO_MQ => '头条小程序',
        ];
    }
}