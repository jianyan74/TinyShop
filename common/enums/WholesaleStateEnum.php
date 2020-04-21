<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * 拼团状态
 *
 * Class WholesaleStateEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class WholesaleStateEnum extends BaseEnum
{
    const NO_PAY = 0;
    const IN = 1;
    const PASS = 2;
    const FAILURE = 3;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::NO_PAY => '未付款',
            self::IN => '未成团',
            self::PASS => '已成团',
            self::FAILURE => '失败',
        ];
    }
}