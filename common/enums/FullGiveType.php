<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * 满减送优惠级别
 *
 * Class FullGiveType
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class FullGiveType extends BaseEnum
{
    const GENERAL = 1;
    const MULTISTAGE = 2;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::GENERAL => '普通优惠',
            self::MULTISTAGE => '多级优惠',
        ];
    }
}