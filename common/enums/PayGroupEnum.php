<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * Class PayGroupEnum
 * @package addons\TinyShop\common\enums
 */
class PayGroupEnum extends BaseEnum
{
    const ORDER = 'order';
    const ORDER_UNITE = 'orderUnite';
    const ORDER_BATCH = 'orderBatch';
    const ORDER_UNITE_BATCH = 'orderUniteBatch';
    const RECHARGE = 'recharge';

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::ORDER => '订单支付',
            self::ORDER_UNITE => '订单混合支付', // 余额 + 第三方支付
            self::ORDER_BATCH => '订单批量支付',
            self::ORDER_UNITE_BATCH => '订单混合批量支付', // 余额 + 第三方支付
            self::RECHARGE => '充值',
        ];
    }

    /**
     * @return array
     */
    public static function getOrderDetailMap(): array
    {
        return [
            self::ORDER => '订单支付',
            self::ORDER_UNITE => '订单混合支付', // 余额 + 第三方支付
            self::ORDER_BATCH => '订单支付',
            self::ORDER_UNITE_BATCH => '订单混合支付', // 余额 + 第三方支付
        ];
    }

    /**
     * @param $key
     * @return string
     */
    public static function getOrderDetailValue($key): string
    {
        return static::getOrderDetailMap()[$key] ?? '';
    }
}
