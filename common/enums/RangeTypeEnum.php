<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * 参加使用类型
 *
 * Class RangeTypeEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class RangeTypeEnum extends BaseEnum
{
    const ALL = 1;
    const ASSIGN_PRODUCT = 2;
    const NOT_ASSIGN_PRODUCT = 3;
    const ASSIGN_CATE = 11;
    const NOT_ASSIGN_CATE = 12;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::ALL => '全部商品参加',
            self::ASSIGN_PRODUCT => '指定商品参加',
            self::NOT_ASSIGN_PRODUCT => '指定商品不参加',
        ];
    }

    /**
     * @return array
     */
    public static function getFullMap(): array
    {
        return [
            self::ALL => '全部商品参加',
            self::ASSIGN_PRODUCT => '指定商品参加',
            self::NOT_ASSIGN_PRODUCT => '指定商品不参加',
            self::ASSIGN_CATE => '指定分类参加',
            self::NOT_ASSIGN_CATE => '指定分类不参加',
        ];
    }

    /**
     * @return array
     */
    public static function getCurtailMap(): array
    {
        return [
            self::ALL => '全部商品参加',
            self::ASSIGN_PRODUCT => '指定商品参加',
        ];
    }

    /**
     * @param $key
     * @return string
     */
    public static function getFullValue($key): string
    {
        return static::getFullMap()[$key] ?? '';
    }
}
