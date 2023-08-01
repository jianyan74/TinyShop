<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * Class ProductExpressShippingTypeEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class ProductExpressShippingTypeEnum extends BaseEnum
{
    const NOT_LOGISTICS = 0;
    const LOGISTICS = 1;

    /**
     * @return string[]
     */
    public static function getMap(): array
    {
        return [
            self::NOT_LOGISTICS => '无需物流',
            self::LOGISTICS => '需要物流',
        ];
    }
}
