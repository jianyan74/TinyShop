<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * 营销预告类型
 *
 * Class MarketingAdvanceTypeEnum
 * @package addons\TinyShop\common\enums
 */
class MarketingAdvanceTypeEnum extends BaseEnum
{
    const IMMEDIATE_NOTICE = 1;
    const ADVANCE_NOTICE = 2;
    const NOT_NOTICE = 3;

    /**
     * @return string[]
     */
    public static function getMap(): array
    {
        return [
            self::IMMEDIATE_NOTICE => '立即预告',
            self::ADVANCE_NOTICE => '活动前 N 小时',
            self::NOT_NOTICE => '不进行预告',
        ];
    }

    /**
     * @return string[]
     */
    public static function getCurtailMap(): array
    {
        return [
            self::IMMEDIATE_NOTICE => '立即预告',
            self::NOT_NOTICE => '不进行预告',
        ];
    }
}
