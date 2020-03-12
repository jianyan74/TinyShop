<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * 阶梯折扣类型
 *
 * Class PreferentialTypeEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class PreferentialTypeEnum extends BaseEnum
{
    const MONEY = 1;
    const DISCOUNT = 2;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::MONEY => '扣减',
            self::DISCOUNT => '折扣',
        ];
    }
}