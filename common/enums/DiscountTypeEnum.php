<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * 折扣类型
 *
 * Class DiscountTypeEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class DiscountTypeEnum extends BaseEnum
{
    const MONEY = 1;
    const DISCOUNT = 2;
    const FIXATION = 3;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::MONEY => '减钱',
            self::DISCOUNT => '折扣',
        ];
    }

    /**
     * @return array
     */
    public static function getAllMap(): array
    {
        return [
            self::MONEY => '减钱',
            self::DISCOUNT => '折扣',
            self::FIXATION => '促销价',
        ];
    }
}
