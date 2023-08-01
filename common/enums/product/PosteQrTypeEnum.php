<?php

namespace addons\TinyShop\common\enums\product;

use common\enums\BaseEnum;

/**
 * Class PosteQrTypeEnum
 * @package addons\TinyShop\common\enums\product
 * @author jianyan74 <751393839@qq.com>
 */
class PosteQrTypeEnum extends BaseEnum
{
    const COMMON_QR = 'common_qr';
    const MINI_PROGRAM_QR = 'mini_program_qr';

    /**
     * @return array|string[]
     */
    public static function getMap(): array
    {
        return [
            self::COMMON_QR => '普通二维码',
            self::MINI_PROGRAM_QR => '小程序二维码',
        ];
    }
}
