<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * 下单类型
 *
 * Class PreviewTypeEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class PreviewTypeEnum extends BaseEnum
{
    const BUY_NOW = 'buy_now';
    const CART = 'cart';
    const POINT_EXCHANGE = 'point_exchange';

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::BUY_NOW => '立即购买',
            self::CART => '购物车',
            self::POINT_EXCHANGE => '积分兑换',
        ];
    }
}