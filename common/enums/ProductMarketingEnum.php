<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * 商品自带营销类型
 *
 * Class ProductMarketingEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class ProductMarketingEnum extends BaseEnum
{
    const GIVE_POINT = 'give_point';
    const LADDER_PREFERENTIAL = 'ladder_preferential';
    const DISCOUNT_PRODUCT = 'discount_product';

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::GIVE_POINT => '赠送积分',
            self::LADDER_PREFERENTIAL => '阶梯优惠',
            self::DISCOUNT_PRODUCT => '限时折扣',
        ];
    }
}