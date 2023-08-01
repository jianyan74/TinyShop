<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;
use common\enums\StatusEnum;

/**
 * Class AdvLocalEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class AdvLocalEnum extends BaseEnum
{
    const INDEX_TOP = 'index_top';
    const INDEX_NEW = 'index_new';
    const INDEX_RECOMMEND = 'index_recommend';
    const INDEX_HOT = 'index_hot';
    const DISCOUNT_TOP = 'discount_top';
    const GROUP_BUY_TOP = 'group_buy_top';
    const BARGAIN_TOP = 'bargain_top';
    const INTEGRAL_TOP = 'integral_top';

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::INDEX_TOP => '首页顶部轮播广告图',
            self::INDEX_NEW => '首页新品广告图',
            self::INDEX_RECOMMEND => '首页推荐广告图',
            self::INDEX_HOT => '首页热门广告图',
            self::DISCOUNT_TOP => '限时折扣顶部广告图',
            self::GROUP_BUY_TOP => '团购顶部广告图',
            self::BARGAIN_TOP => '砍价顶部广告图',
            self::INTEGRAL_TOP => '积分商城顶部广告图',
        ];
    }

    /**
     * @return array
     */
    public static function config()
    {
        return [
            self::INDEX_TOP => [
                'name' => self::getValue(self::INDEX_TOP),
                'multiple' => 1,
            ],
            self::INDEX_NEW => [
                'name' => self::getValue(self::INDEX_NEW),
                'multiple' => 0,
            ],
            self::INDEX_HOT => [
                'name' => self::getValue(self::INDEX_HOT),
                'multiple' => 0,
            ],
            self::INDEX_RECOMMEND => [
                'name' => self::getValue(self::INDEX_RECOMMEND),
                'multiple' => 0,
            ],
            self::DISCOUNT_TOP => [
                'name' => self::getValue(self::DISCOUNT_TOP),
                'multiple' => 1,
            ],
            self::GROUP_BUY_TOP => [
                'name' => self::getValue(self::GROUP_BUY_TOP),
                'multiple' => 1,
            ],
            self::BARGAIN_TOP => [
                'name' => self::getValue(self::BARGAIN_TOP),
                'multiple' => 1,
            ],
            self::INTEGRAL_TOP => [
                'name' => self::getValue(self::INTEGRAL_TOP),
                'multiple' => 1,
            ],
        ];
    }
}
