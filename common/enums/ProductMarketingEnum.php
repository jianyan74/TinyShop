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
    const LADDER_PREFERENTIAL = 'ladder_preferential';
    const MEMBER_DISCOUNT = 'member_discount';
    const DISCOUNT = 'discount';
    const FULL_GIVE = 'full_give';
    const FULL_MAIL = 'full_mail';
    const COMBINATION = 'combination';
    const BARGAIN = 'bargain';
    const GROUP_BUY = 'group_buy';
    const WHOLESALE = 'wholesale';
    const SUBSCRIBE_BUY = 'subscribe_buy';

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            // 1级权重
            self::DISCOUNT => '限时折扣',
            self::COMBINATION => '组合套餐',
            self::BARGAIN => '砍价活动',
            self::GROUP_BUY => '团购',
            self::WHOLESALE => '拼团',
            self::SUBSCRIBE_BUY => '预约购买',
            // 2级权重
            self::LADDER_PREFERENTIAL => '阶梯优惠',
            self::MEMBER_DISCOUNT => '会员折扣',
            // 3级权重
            self::FULL_GIVE => '满减送活动',
            self::GIVE_POINT => '赠送积分',
            // 4级权重
            self::FULL_MAIL => '满额包邮',
            self::COUPON => '优惠券',
        ];
    }

    /**
     * 获取互斥营销
     *
     * @return array
     */
    public static function mutualMarketing()
    {
        return [
            self::DISCOUNT, // 限时折扣
            self::BARGAIN,  // 砍价活动
            self::GROUP_BUY, // 团购
            self::WHOLESALE, // 拼团
        ];
    }
}