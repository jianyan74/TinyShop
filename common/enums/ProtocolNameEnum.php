<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * 协议类型
 *
 * Class ProtocolNameEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class ProtocolNameEnum extends BaseEnum
{
    const REGISTER = 'register';
    const PRIVACY = 'privacy';
    const RECHARGE = 'recharge';
    const MEMBER_CARD = 'member_card';
    const MEMBER_CARD_RIGHTS = 'member_card_rights';

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::REGISTER => '注册协议',
            self::PRIVACY => '隐私协议',
            self::RECHARGE => '充值协议',
            self::MEMBER_CARD => '会员开卡协议',
            self::MEMBER_CARD_RIGHTS => '会员权益细则',
        ];
    }
}
