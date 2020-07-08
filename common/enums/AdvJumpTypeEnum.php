<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * Class AdvJumpTypeEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class AdvJumpTypeEnum extends BaseEnum
{
    const PRODUCT_VIEW = 'product_view';
    const ARTICLE_VIEW = 'article_view';
    const COUPON_VIEW = 'coupon_view';
    const HELPER_VIEW = 'helper_view';
    const MINI_PROGRAM_LIVE_VIEW = 'mini_program_live_view';
    const NOTIFY_ANNOUNCE_VIEW = 'notify_announce_view';


    // 列表
    const PRODUCT_LIST_FOR_CATE = 'product_list_for_cate';

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::PRODUCT_VIEW => '产品详情',
            // self::ARTICLE_VIEW => '文章详情',
            self::MINI_PROGRAM_LIVE_VIEW => '小程序直播详情',
            self::NOTIFY_ANNOUNCE_VIEW => '公告详情',
            self::HELPER_VIEW => '站点帮助详情',
            self::COUPON_VIEW => '优惠券详情',
            self::PRODUCT_LIST_FOR_CATE => '某分类下产品列表',
        ];
    }
}