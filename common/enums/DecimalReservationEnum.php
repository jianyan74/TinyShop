<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * 价格保留方式
 *
 * Class DecimalReservationEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class DecimalReservationEnum extends BaseEnum
{
    const DEFAULT = -1;
    const CLEAR_DECIMAL_TWO = 0;
    const CLEAR_DECIMAL_ONE = 1;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::DEFAULT => '保留角和分',
            self::CLEAR_DECIMAL_TWO => '抹去角和分',
            self::CLEAR_DECIMAL_ONE => '抹去分',
        ];
    }
}