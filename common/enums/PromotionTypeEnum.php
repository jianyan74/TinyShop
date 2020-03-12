<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * 营销活动类型
 *
 * Class PromotionTypeEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class PromotionTypeEnum extends BaseEnum
{
    // 满包邮
    // 满减送
    // 优惠券
    // 组合套餐
    // 砍价活动
    // 团购
    // 限时折扣
    // 拼团(待定)

    public static function getMap(): array
    {
        return  [];
    }
}