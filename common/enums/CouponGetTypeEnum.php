<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * 优惠券领取类型
 *
 * Class CouponGetTypeEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class CouponGetTypeEnum extends BaseEnum
{
    const ORDER = 1;
    const ONESELF = 2;
    const MANAGER = 3;
    const REGISTER = 4;
    const REDEEM_CODE = 5;
    const MEMBER_CARD = 6;
    const MEMBER_INVITE = 7;
    const RECHARGE = 8;

    /**
     * @return array|string[]
     */
    public static function getMap(): array
    {
        return [
            self::ORDER => '订单赠送',
            self::ONESELF => '主动领取',
            self::MANAGER => '管理员赠送',
            self::REGISTER => '注册赠送',
            self::REDEEM_CODE => '兑换码兑换',
            self::MEMBER_CARD => '会员卡赠送',
            self::MEMBER_INVITE => '邀请好友赠送',
            self::RECHARGE => '充值',
        ];
    }

    /**
     * @return int[]
     */
    public static function getShowMap(): array
    {
        return [
            self::ORDER, // 订单赠送
            self::MANAGER, // 管理员赠送
            self::REGISTER, // 注册赠送
            self::MEMBER_CARD, // 会员卡赠送
            self::MEMBER_INVITE, // 邀请好友赠送
            self::RECHARGE, // 充值赠送
        ];
    }
}
