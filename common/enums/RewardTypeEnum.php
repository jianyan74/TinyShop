<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * 奖励类型
 *
 * Class RewardTypeEnum
 * @package addons\TinyShop\common\enums
 */
class RewardTypeEnum extends BaseEnum
{
    const NOT = 0;
    const BALANCE = 1;
    const RED_PACKET = 2;
    const COUPON = 3;
    const POINT = 4;
    const PRODUCT = 5;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::NOT => '不处理',
            self::BALANCE => '消费余额',
            self::RED_PACKET => '现金红包',
            self::COUPON => '优惠券',
            self::POINT => '积分',
            self::PRODUCT => '商品',
        ];
    }

    /**
     * @return array
     */
    public static function getCurtailMap(): array
    {
        return [
            self::BALANCE => '消费余额',
            self::COUPON => '优惠券',
            self::POINT => '积分',
        ];
    }
}
