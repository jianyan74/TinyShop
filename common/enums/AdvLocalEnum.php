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
    const CATE_TOP = 'cate_top';

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
            self::CATE_TOP => '分类顶部广告图',
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
                'multiple' => StatusEnum::ENABLED, // 轮播多图
            ],
            self::INDEX_NEW => [
                'name' => self::getValue(self::INDEX_NEW),
                'multiple' => StatusEnum::DISABLED, // 轮播多图
            ],
            self::INDEX_HOT => [
                'name' => self::getValue(self::INDEX_HOT),
                'multiple' => StatusEnum::DISABLED, // 轮播多图
            ],
            self::INDEX_RECOMMEND => [
                'name' => self::getValue(self::INDEX_RECOMMEND),
                'multiple' => StatusEnum::DISABLED, // 轮播多图
            ],
            self::CATE_TOP => [
                'name' => self::getValue(self::CATE_TOP),
                'multiple' => StatusEnum::DISABLED, // 轮播多图
            ],
        ];
    }
}