<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;
use common\models\merchant\Merchant;
use addons\TinyShop\common\models\product\Product;

/**
 * 公用映射类
 *
 * 必须带有字段 collect_num, transmit_num, nice_num
 *
 * Class CommonModelMapEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class CommonModelMapEnum extends BaseEnum
{
    const PRODUCT = 'product';
    const MERCHANT = 'merchant';

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::PRODUCT => Product::class,
            self::MERCHANT => Merchant::class,
        ];
    }
}
