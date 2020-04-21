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
    const LOGISTICS = 1;
    const PICKUP = 2;
    const CASH_AGAINST = 3;
    const LOCAL_DISTRIBUTION = 4;
    const VIRTUAL = 5;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::LOGISTICS => '物流配送',
            self::PICKUP => '买家自提',
            self::CASH_AGAINST => '货到付款',
            self::LOCAL_DISTRIBUTION => '本地配送',
            self::VIRTUAL => '虚拟商品',
        ];
    }
}