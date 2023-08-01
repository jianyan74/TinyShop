<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * 营销类型类型
 *
 * Class MarketingEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class MarketingEnum extends BaseEnum
{
    const ASSOCIATION_SOLITAIRE = 'association_solitaire';
    const GIVE_POINT = 'give_point';
    const GIVE_GROWTH = 'give_growth';
    const USE_POINT = 'use_point';
    const COUPON = 'coupon';
    const COUPON_IN = 'coupon_in'; // 参加的商品
    const COUPON_NOT_IN = 'coupon_not_in'; // 不参加的商品
    const LADDER_PREFERENTIAL = 'ladder_preferential';
    const MEMBER_DISCOUNT = 'member_discount';
    const MEMBER_FIRST_BUY = 'member_first_buy';
    const MEMBER_REGISTER = 'member_register';
    const MEMBER_INVITE = 'member_invite';
    const MEMBER_RECHARGE_CONFIG = 'member_recharge_config';
    const MEMBER_CARD = 'member_card';
    // 营销
    const DISCOUNT = 'discount';
    const SEC_KILL = 'sec_kill';
    const FULL_GIVE = 'full_give';
    const FULL_MAIL = 'full_mail';
    const COMBINATION = 'combination';
    const BARGAIN = 'bargain';
    const GROUP_BUY = 'group_buy';
    const WHOLESALE = 'wholesale';
    const SECOND_HALF_DISCOUNT = 'second_half_discount';
    const BALE = 'bale';
    const PLUS_BUY = 'plus_buy';
    const PLUS_BUY_JOIN = 'plus_buy_join'; // 参加的商品
    const PLUS_BUY_TRADE = 'plus_buy_trade'; // 换购的商品
    const PRE_SELL = 'pre_sell';
    const PRE_SELL_DEDUCTION = 'pre_sell_deduction';
    const PLATFORM_COUPON = 'platform_coupon';
    const GIFT = 'gift';
    const POINT_EXCHANGE = 'point_exchange';

    // 其他(下单方式)
    const BUY_NOW = 'buy_now';
    const BUY_AGAIN = 'buy_again';
    const CART = 'cart';
    const TO_STORE = 'to_store';

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            // 1级权重
            self::DISCOUNT => '限时折扣',
            self::SEC_KILL => '限时秒杀',
            self::COMBINATION => '组合套餐',
            self::BARGAIN => '砍价',
            self::GROUP_BUY => '团购', // 满N件享折扣
            self::WHOLESALE => '拼团',
            self::SECOND_HALF_DISCOUNT => '第"2"件半价',
            self::BALE => '打包一口价',
            // 2级权重
            self::LADDER_PREFERENTIAL => '阶梯优惠',
            self::MEMBER_DISCOUNT => '会员折扣',
            // 3级权重
            self::FULL_GIVE => '满减送',
            self::GIVE_POINT => '赠送积分',
            self::GIVE_GROWTH => '赠送成长值',
            self::MEMBER_RECHARGE_CONFIG => '充值套餐',
            self::PLUS_BUY => '超值换购',
            // 4级权重
            self::FULL_MAIL => '满额包邮',
            self::MEMBER_FIRST_BUY => '新用户',
            self::COUPON => '优惠券',
            self::USE_POINT => '积分抵扣',
            self::PLATFORM_COUPON => '平台优惠券',
            // 5级权重
            self::PRE_SELL => '预售',
            self::PRE_SELL_DEDUCTION => '预付金膨胀',
            self::ASSOCIATION_SOLITAIRE => '社群接龙',
            self::PLUS_BUY_JOIN => '参与超值换购',
            self::PLUS_BUY_TRADE => '超值换购',
            self::POINT_EXCHANGE => '积分兑换',
            // 其他(下单方式)
            self::BUY_NOW => '立即购买',
            self::BUY_AGAIN => '再次购买',
            self::CART => '购物车',
        ];
    }

    /**
     * @return array
     */
    public static function getBackendSearchMap(): array
    {
        return [
            self::DISCOUNT => '限时折扣',
            self::SEC_KILL => '限时秒杀',
            self::BARGAIN => '砍价',
            self::GROUP_BUY => '团购',
            self::WHOLESALE => '拼团',
            self::PRE_SELL => '预售',
            self::SECOND_HALF_DISCOUNT => '第"2"件半价',
            self::BALE => '打包一口价',
            self::POINT_EXCHANGE => '积分兑换',
        ];
    }

    /**
     * 获取一般的营销类型 (购物车)
     *
     * @return array
     */
    public static function ordinaryMarketing()
    {
        return [
            MarketingEnum::BALE,
            MarketingEnum::SECOND_HALF_DISCOUNT,
            MarketingEnum::DISCOUNT,
            MarketingEnum::GROUP_BUY,
        ];
    }

    /**
     * 会员价互斥营销
     *
     * @return string[]
     */
    public static function notMemberDiscount()
    {
        return [
            MarketingEnum::PRE_SELL,
            MarketingEnum::DISCOUNT,
            MarketingEnum::SEC_KILL,
            MarketingEnum::WHOLESALE,
            MarketingEnum::BARGAIN,
            MarketingEnum::GROUP_BUY,
            MarketingEnum::COMBINATION,
            MarketingEnum::BALE,
            MarketingEnum::SECOND_HALF_DISCOUNT,
            MarketingEnum::POINT_EXCHANGE,
        ];
    }

    /**
     * 独立库存
     *
     *    砍价不算单独的(提前扣过了)
     *
     * @return string[]
     */
    public static function independenceStock()
    {
        return [
            MarketingEnum::SEC_KILL,
            MarketingEnum::POINT_EXCHANGE,
        ];
    }

    /**
     * 独立库存
     *
     *     主要用户商品详情显示
     *
     * @return string[]
     */
    public static function independenceStockByAll()
    {
        return [
            MarketingEnum::BARGAIN,
            MarketingEnum::SEC_KILL,
            MarketingEnum::POINT_EXCHANGE,
        ];
    }
}
