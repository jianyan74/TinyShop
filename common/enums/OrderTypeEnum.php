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

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::ORDINARY => '普通订单',
        ];
    }

    /**
     * @return array
     */
    public static function getAliasMap(): array
    {
        return [
            self::ORDINARY => 'Ordinary',
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
        ];
    }
}