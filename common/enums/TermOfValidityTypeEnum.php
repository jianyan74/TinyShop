<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * Class TermOfValidityTypeEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class TermOfValidityTypeEnum extends BaseEnum
{
    const FIXATION = 0;
    const GET = 1;

    /**
     * @return string[]
     */
    public static function getMap(): array
    {
        return[
            self::FIXATION => '固定时间',
            self::GET => '领到券当日开始 N 天内有效',
        ];
    }
}
