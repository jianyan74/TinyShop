<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * 预售发货类型
 *
 * Class PresellDeliveryTypeEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class PresellDeliveryTypeEnum extends BaseEnum
{
    const FIXATION_TIME = 1;
    const DAY = 2;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::FIXATION_TIME => '按照预售发货时间',
            self::DAY => '按照预售发货天数',
        ];
    }
}