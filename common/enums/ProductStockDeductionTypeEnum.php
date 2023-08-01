<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * Class ProductStockDeductionTypeEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class ProductStockDeductionTypeEnum extends BaseEnum
{
    const PAY = 1;
    const CREATE = 2;

    /**
     * @return string[]
     */
    public static function getMap(): array
    {
        return [
            self::PAY => '付款减库存',
            self::CREATE => '拍下减库存',
        ];
    }
}
