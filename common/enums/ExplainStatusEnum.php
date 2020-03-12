<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * 评价状态
 *
 * Class ExplainStatusEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class ExplainStatusEnum extends BaseEnum
{
    const DEAULT = 0;
    const EVALUATE = 1;
    const SUPERADDITION = 2;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::DEAULT => '未评价',
            self::EVALUATE => '已评价',
            self::SUPERADDITION => '已追加评价',
        ];
    }
}