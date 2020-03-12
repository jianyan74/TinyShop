<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * 使用类型
 *
 * Class RangeTypeEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class RangeTypeEnum extends BaseEnum
{
    const ALL = 1;
    const ASSIGN = 2;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::ALL => '全场',
            self::ASSIGN => '部分',
        ];
    }
}