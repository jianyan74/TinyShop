<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * Class PointConfigDeductionTypeEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class PointConfigDeductionTypeEnum extends BaseEnum
{
    const NOT = 0;
    const MONEY = 1;
    const RATE = 2;

    /**
     * @return string[]
     */
    public static function getMap(): array
    {
        return [
            self::NOT => '不限制',
            self::MONEY => '订单金额',
            self::RATE => '订单比率',
        ];
    }
}
