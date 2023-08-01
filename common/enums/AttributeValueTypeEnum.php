<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * Class AttributeValueTypeEnum
 * @package addons\TinyShop\common\enums
 */
class AttributeValueTypeEnum extends BaseEnum
{
    const TEXT = 1;
    const RADIO = 2;
    const CHECK = 3;
    
    public static function getMap(): array
    {
        return [
            self::TEXT => '输入框',
            self::RADIO => '单选框',
            self::CHECK => '复选框',
        ];
    }
}