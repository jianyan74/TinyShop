<?php

namespace addons\TinyShop\common\enums\product;

use common\enums\BaseEnum;

/**
 * Class PosteCoverTypeEnum
 * @package addons\TinyShop\common\enums\product
 * @author jianyan74 <751393839@qq.com>
 */
class PosteCoverTypeEnum extends BaseEnum
{
    const ROUNDNESS = 'roundness';
    const QUADRATE = 'quadrate';

    /**
     * @return array|string[]
     */
    public static function getMap(): array
    {
        return [
            self::ROUNDNESS => '圆形',
            self::QUADRATE => '正方形',
        ];
    }
}
