<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * 订单类型
 *
 * Class OrderTypeEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class OrderTypeEnum extends BaseEnum
{
    const ORDINARY = 1;
    const VIRTUAL = 2;
    const COMBINATION = 3;
    const WHOLESALE = 4;
    const GROUP_BUY = 5;
    const PRESELL_BUY = 6;
    const BARGAIN = 7;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::ORDINARY => '普通订单',
            self::VIRTUAL => '虚拟订单',
            self::COMBINATION => '组合订单',
            self::WHOLESALE => '拼团订单',
            self::GROUP_BUY => '团购订单',
            self::PRESELL_BUY => '预售订单',
            self::BARGAIN => '砍价订单',
        ];
    }

    /**
     * @return array
     */
    public static function getAliasMap(): array
    {
        return [
            self::ORDINARY => 'Ordinary',
            self::VIRTUAL => 'Virtual',
            self::COMBINATION => 'Combination',
            self::WHOLESALE => 'Wholesale',
            self::GROUP_BUY => 'GroupBuy',
            self::PRESELL_BUY => 'PresellBuy',
            self::BARGAIN => 'Bargain',
        ];
    }

    /**
     * @param $key
     * @return string
     */
    public static function getAlias($key): string
    {
        return static::getAliasMap()[$key] ?? '';
    }

    /**
     * 正常的订单类型
     *
     * @return array
     */
    public static function normal()
    {
        return [
            self::ORDINARY, // 普通订单
            self::COMBINATION, // 组合订单
            self::WHOLESALE, // 拼团订单
            self::GROUP_BUY, // 团购订单
            self::PRESELL_BUY, // 预售
            self::BARGAIN, // 砍价订单
        ];
    }
}