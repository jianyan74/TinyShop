<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;
use addons\TinyShop\common\models\product\Product;

/**
 * 公用映射类
 *
 * 必须带有字段 collect_num, transmit_num, nice_num
 *
 * Class CommonTypeEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class CommonTypeEnum extends BaseEnum
{
    const PRODUCT = 'product';

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::PRODUCT => Product::class,
        ];
    }
}