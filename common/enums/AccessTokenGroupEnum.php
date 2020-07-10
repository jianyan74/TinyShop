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
    const WECHAT = 'tinyShopWechat'; // 微信H5
    const WECHAT_MP = 'tinyShopWechatMp'; // 微信小程序
    const ALI_MP = 'tinyShopAliMp'; // 支付宝小程序
    const QQ_MP = 'tinyShopQqMp'; // QQ小程序
    const BAIDU_MP = 'tinyShopBaiduMp'; // 百度小程序
    const DING_TALK_MP = 'tinyShopDingTalkMp'; // 钉钉小程序
    const TOU_TIAO_MP = 'tinyShopTouTiaoMp'; // 头条小程序

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
            self::WECHAT_MP => '微信小程序',
            self::ALI_MP => '支付宝小程序',
            self::QQ_MP => 'QQ小程序',
            self::BAIDU_MP => '百度小程序',
            self::DING_TALK_MP => '钉钉小程序',
            self::TOU_TIAO_MP => '头条小程序',
        ];
    }
}