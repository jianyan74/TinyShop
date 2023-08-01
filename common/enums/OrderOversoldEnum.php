<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * Class OrderOversoldEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class OrderOversoldEnum extends BaseEnum
{
    const MANUAL_WORK = 1;
    const AUTO_REFUND = 2;

    /**
     * @return string[]
     */
    public static function getMap(): array
    {
        return [
            self::MANUAL_WORK => '人工处理',
            // self::AUTO_REFUND => '系统自动退款',
        ];
    }
}
