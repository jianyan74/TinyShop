<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * Class MerchantAddressTypeEnum
 * @package addons\TinyShop\common\enums
 */
class MerchantAddressTypeEnum extends BaseEnum
{
    const DEFAULT = 0;
    const SALES_RETURN = 1;

    /**
     * @return string[]
     */
    public static function getMap(): array
    {
        return [
            self::DEFAULT => '未定义',
            self::SALES_RETURN => '退货地址',
        ];
    }
}