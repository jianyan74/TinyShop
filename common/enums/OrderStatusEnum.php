<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * 订单状态
 *
 * Class OrderStatusEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class OrderStatusEnum extends BaseEnum
{
    const NOT_PAY = 0;
    const PAY = 10;
    const PART_SHIPMENTS = 19;
    const SHIPMENTS = 20;
    const SING = 30;
    const ACCOMPLISH = 40;
    const REFUND_APPLY = -10;
    const REFUND_ING = -20;
    const REFUND = -30;
    const REPEAL = -40;
    const MEMBER_REPEAL = -41;
    const REPEAL_APPLY = -50;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::NOT_PAY => '待付款',
            self::PAY => '待发货', // 已付款
            self::PART_SHIPMENTS => '部分发货',
            self::SHIPMENTS => '已发货',
            self::SING => '已收货',
            self::ACCOMPLISH => '已完成',
            self::REPEAL => '已关闭', // 全部商品售后之后，订单取消
            self::MEMBER_REPEAL => '主动取消', // 用户主动取消或待付款超时取消
            self::REFUND_APPLY => '退货申请',
            self::REFUND_ING => '退款中',
            self::REFUND => '已退货',
            self::REPEAL_APPLY => '撤销申请',
        ];
    }

    /**
     * @return array
     */
    public static function getBackendMap(): array
    {
        return [
            self::NOT_PAY => '待付款',
            self::PAY => '待发货', // 已付款
            // 骑手
            self::SHIPMENTS => '已发货',
            self::SING => '已收货',
            self::ACCOMPLISH => '已完成',
            self::REPEAL => '已关闭',
            self::REFUND_ING => '退款中',
        ];
    }

    /**
     * 已下单的状态未被关闭的
     *
     * @return array
     */
    public static function haveBought()
    {
        return array_keys([
            self::NOT_PAY => '待付款',
            self::PAY => '待发货', // 已付款
            self::SHIPMENTS => '已发货',
            self::SING => '已收货',
            self::ACCOMPLISH => '已完成',
            self::REFUND_APPLY => '退货申请',
            self::REFUND_ING => '退款中',
            self::REFUND => '已退货',
            self::REPEAL_APPLY => '撤销申请',
        ]);
    }
}
