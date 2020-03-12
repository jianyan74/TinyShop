<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * 评价类型
 *
 * Class ExplainTypeEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class ExplainTypeEnum extends BaseEnum
{
    const NEGATIVE = 1;
    const ORDINARY = 2;
    const GOOD = 3;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::NEGATIVE => '差评',
            self::ORDINARY => '中评',
            self::GOOD => '好评',
        ];
    }

    /**
     * @param $scores
     * @return int
     */
    public static function scoresToType($scores)
    {
        if (in_array($scores, [1, 2])) {
            return self::NEGATIVE;
        }

        if (in_array($scores, [3])) {
            return self::ORDINARY;
        }

        return self::GOOD;
    }
}