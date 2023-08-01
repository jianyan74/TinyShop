<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * 积分类型
 *
 * Class PointExchangeTypeEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class PointExchangeTypeEnum extends BaseEnum
{
    const PRODUCT = 1;
    const COUPON = 2;
    const BALANCE = 3;

    /**
     * @return array
     */
    public static function getMap(): array
    {
       return [
           self::PRODUCT => '商品',
           // self::COUPON => '优惠券',
           // self::BALANCE => '余额',
       ];
    }
}
