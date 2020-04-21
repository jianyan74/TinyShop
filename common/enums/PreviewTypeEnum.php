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
    const PRESELL_BUY = 'presell_buy';
    const COMBINATION = 'combination';
    const GROUP_BUY = 'group_buy';
    const WHOLESALE = 'wholesale';
    const POINT_EXCHANGE = 'point_exchange';
    const VIRTUAL = 'virtual';
    const DISCOUNT = 'discount';

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::BUY_NOW => '立即购买',
            self::CART => '购物车',
            self::PRESELL_BUY => '预售',
            self::COMBINATION => '组合套餐',
            self::GROUP_BUY => '团购',
            self::WHOLESALE => '拼团',
            self::POINT_EXCHANGE => '积分兑换',
            self::VIRTUAL => '虚拟下单',
        ];
    }
}