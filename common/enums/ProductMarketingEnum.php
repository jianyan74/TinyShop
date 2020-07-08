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
    const COUPON = 'coupon';
    const FULL_MAIL = 'full_mail';

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::GIVE_POINT => '赠送积分',
            self::FULL_MAIL => '满额包邮',
            self::COUPON => '优惠券',
        ];
    }
}