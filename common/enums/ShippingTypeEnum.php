<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * 配送类型
 *
 * Class ShippingTypeEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class ShippingTypeEnum extends BaseEnum
{
    const MERCHANT = 1;
    const VISIT = 2;
    const LOCAL_DISTRIBUTION = 3;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::MERCHANT => '物流配送',
            self::VISIT => '买家自提',
            self::LOCAL_DISTRIBUTION => '本地配送',
        ];
    }
}