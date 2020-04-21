<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * 退换货状态
 *
 * Class RefundStatusEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class RefundStatusEnum extends BaseEnum
{
    const APPLY = 1;
    const SALES_RETURN = 2;
    const AFFIRM_SALES_RETURN = 3;
    const AFFIRM_RETURN_MONEY = 4;
    const CONSENT = 5;
    const NO_PASS_ALWAYS = -1;
    const CANCEL = -2;
    const NO_PASS = -3;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::APPLY => '买家退款申请', // 发起了退款申请,等待卖家处理
            self::SALES_RETURN => '等待买家退货', // 卖家已同意退款申请,等待买家退货
            self::AFFIRM_SALES_RETURN => '等待卖家确认收货', // 买家已退货,等待卖家确认收货
            self::AFFIRM_RETURN_MONEY => '等待卖家确认退款',
            self::CONSENT => '卖家同意退款',
            self::NO_PASS_ALWAYS => '退款已拒绝',
            self::CANCEL => '退款已关闭',
            self::NO_PASS => '退款申请不通过',
        ];
    }

    /**
     * 再退款(售后)状态
     *
     * @return array
     */
    public static function refund()
    {
        return [
            self::APPLY,
            self::SALES_RETURN,
            self::AFFIRM_SALES_RETURN,
            self::AFFIRM_RETURN_MONEY,
        ];
    }

    /**
     * 可发货状态
     *
     * @return array
     */
    public static function deliver()
    {
        return [
            0,
            self::NO_PASS_ALWAYS,
            self::CANCEL,
            self::NO_PASS,
        ];
    }

    /**
     * 可评价状态
     */
    public static function evaluate()
    {
        return [
            self::NO_PASS_ALWAYS,
            self::CANCEL,
            0
        ];
    }
}