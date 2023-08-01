<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;
use common\enums\AccessTokenGroupEnum as BaseAccessTokenGroupEnum;

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
    const WECHAT_MP = 'tinyShopWechatMp'; // 微信公众号
    const WECHAT_MINI = 'tinyShopWechatMini'; // 微信小程序
    const ALI_MINI = 'tinyShopAliMini'; // 支付宝小程序
    const QQ_MINI = 'tinyShopQqMini'; // QQ小程序
    const BAIDU_MINI = 'tinyShopBaiduMini'; // 百度小程序
    const DING_TALK_MINI = 'tinyShopDingTalkMini'; // 钉钉小程序
    const BYTEDANCE_MINI = 'tinyShopBytedanceMini'; // 字节跳动小程序
    // 开放平台
    const WECHAT = 'tinyShopWechat'; // 微信开放平台
    const APPLE = 'tinyShopApple'; // 苹果

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::DEFAULT => '默认',
            self::IOS => 'iOS',
            self::ANDROID => 'Android',
            self::APP => 'App',
            self::H5 => 'H5',
            self::PC => 'Pc',
            self::WECHAT_MP => '微信',
            self::WECHAT_MINI => '微信小程序',
            self::ALI_MINI => '支付宝小程序',
            self::QQ_MINI => 'QQ小程序',
            self::BAIDU_MINI => '百度小程序',
            self::DING_TALK_MINI => '钉钉小程序',
            self::BYTEDANCE_MINI => '字节跳动小程序',
        ];
    }

    /**
     * 关联系统枚举
     *
     * @param $key
     * @return mixed|string
     */
    public static function relevance($key)
    {
        $list = [
            self::DEFAULT => BaseAccessTokenGroupEnum::DEFAULT,
            self::IOS => BaseAccessTokenGroupEnum::IOS,
            self::ANDROID => BaseAccessTokenGroupEnum::ANDROID,
            self::APP => BaseAccessTokenGroupEnum::APP,
            self::H5 => BaseAccessTokenGroupEnum::H5,
            self::PC => BaseAccessTokenGroupEnum::PC,
            self::WECHAT => BaseAccessTokenGroupEnum::WECHAT,
            self::WECHAT_MP => BaseAccessTokenGroupEnum::WECHAT_MP,
            self::WECHAT_MINI => BaseAccessTokenGroupEnum::WECHAT_MINI,
            self::ALI_MINI => BaseAccessTokenGroupEnum::ALI_MINI,
            self::QQ_MINI => BaseAccessTokenGroupEnum::QQ_MINI,
            self::BAIDU_MINI => BaseAccessTokenGroupEnum::BAIDU_MINI,
            self::DING_TALK_MINI => BaseAccessTokenGroupEnum::DING_TALK_MINI,
            self::BYTEDANCE_MINI => BaseAccessTokenGroupEnum::BYTEDANCE_MINI,
        ];

        return $list[$key] ?? '';
    }
}
