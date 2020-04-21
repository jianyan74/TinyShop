<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * 虚拟卡卷/点卡使用状态
 *
 * Class ProductVirtualStateEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class ProductVirtualStateEnum extends BaseEnum
{
    const PASSED = -2;
    const LOSE = -1;
    const NORMAL = 0;
    const USE = 1;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::PASSED => '未发放',
            self::LOSE => '已失效',
            self::NORMAL => '待使用',
            self::USE => '已使用',
        ];
    }
}