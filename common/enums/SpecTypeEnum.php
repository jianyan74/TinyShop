<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * 规格
 *
 * Class SpecTypeEnum
 * @package addons\TinyShop\common\enums
 */
class SpecTypeEnum extends BaseEnum
{
    const TEXT = 1;
    const COLOR = 2;
    const IMAGE = 3;

    /**
     * @return string[]
     */
    public static function getMap(): array
    {
        return [
            self::TEXT => '文字',
            // self::COLOR => '颜色',
            self::IMAGE => '图片',
        ];
    }
}
