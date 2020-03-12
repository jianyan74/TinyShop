<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * 配送状态
 *
 * Class ShippingStatusEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class ShippingStatusEnum extends BaseEnum
{
    const UN_SHIPPED = 0;
    const DELIVERED = 1;
    const RECEIVED = 2;
    const STOCK_UP = 3;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::UN_SHIPPED => '未发货',
            self::DELIVERED => '已发货',
            self::RECEIVED => '已收货',
            self::STOCK_UP => '备货中',
        ];
    }
}