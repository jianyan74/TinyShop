<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * Class RefundReasonEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class RefundReasonEnum extends BaseEnum
{
    const NEGOTIATED = 'negotiated';
    const BUY_THE_WRONG = 'buy_the_wrong';
    const QUALITY_PROBLEM = 'quality_problem';
    const NOT_RECEIVED = 'not_received';
    const OTHER = 'other';

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::NEGOTIATED => '买/卖双方协商一致',
            self::BUY_THE_WRONG => '买错/买多/不想要了',
            self::QUALITY_PROBLEM => '商品质量问题',
            self::NOT_RECEIVED => '未收到货物',
            self::OTHER => '其他',
        ];
    }
}