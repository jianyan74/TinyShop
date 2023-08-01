<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;
use addons\TinyShop\common\models\marketing\CouponType;
use addons\TinyShop\common\models\order\AfterSale;
use addons\TinyShop\common\models\order\Order;
use addons\TinyShop\common\models\order\OrderProduct;

/**
 * 提醒方法
 *
 * Class SubscriptionActionEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class SubscriptionActionEnum extends BaseEnum
{
    const MESSAGE = 'message';
    const ANNOUNCE = 'announce';
    /** @var string 订单提醒 */
    const ORDER_CONSIGN = 'order_consign';
    const ORDER_RECEIVING = 'order_receiving';
    const ORDER_PAY = 'order_pay';
    const ORDER_CANCEL = 'order_cancel';
    const ORDER_AFTER_SALE_APPLY = 'order_after_sale_apply';
    const ORDER_STOCK_UP_ACCOMPLISH = 'order_stock_up_accomplish';
    const ORDER_RETURN_MONEY = 'order_return_money';
    const ORDER_RETURN_MEMBER_DELIVER = 'order_return_member_deliver';
    const ORDER_RETURN_APPLY_CLOSE = 'order_return_apply_close';
    const ORDER_WHOLESALE_OPEN = 'order_wholesale_open';
    const ORDER_WHOLESALE_JOIN = 'order_wholesale_join';
    const ORDER_WHOLESALE_ACCOMPLISH = 'order_wholesale_accomplish';
    const ORDER_WHOLESALE_CLOSE = 'order_wholesale_close';
    const ORDER_BARGAIN_FRIEND_JOIN = 'order_bargain_friend_join';
    const ORDER_BARGAIN_ACCOMPLISH = 'order_bargain_accomplish';
    const ORDER_BARGAIN_CLOSE = 'order_bargain_close';
    const ORDER_VIRTUAL = 'order_virtual';
    /** @var string 优惠券提醒 */
    const COUPON_GIVE = 'coupon_give';
    const COUPON_CLOSE = 'coupon_close';
    /** @var string 佣金提醒 */
    const COMMISSION = 'commission';
    /** @var string 骑手 */
    const ORDER_CABALLERO_RECEIVING = 'order_caballero_receiving';
    const ORDER_CABALLERO_CANCEL = 'order_caballero_cancel';
    const INVOICE_APPLY = 'invoice_apply';
    const OPINION_CREATE = 'opinion_create';

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::ANNOUNCE => '公告提醒',
            self::MESSAGE => '私信提醒',
            self::ORDER_CONSIGN => '订单发货',
            self::ORDER_CANCEL => '订单退单',
            self::ORDER_RECEIVING => '商家已接单',
            self::ORDER_CABALLERO_RECEIVING => '骑手已接单',
            self::ORDER_CABALLERO_CANCEL => '骑手已取消',
            self::ORDER_PAY => '新订单',
            self::ORDER_STOCK_UP_ACCOMPLISH => '备货完成',
            self::ORDER_AFTER_SALE_APPLY => '订单申请退款',
            self::ORDER_RETURN_MEMBER_DELIVER => ' 填写退/换货物流单号',
            self::ORDER_RETURN_APPLY_CLOSE => ' 填写退/换货物流单号',
            self::ORDER_RETURN_MONEY => '收到订单退款',
            self::ORDER_WHOLESALE_OPEN => '拼团开团成功',
            self::ORDER_WHOLESALE_JOIN => '拼团参团成功',
            self::ORDER_WHOLESALE_ACCOMPLISH => '拼团成功',
            self::ORDER_WHOLESALE_CLOSE => '拼团失败',
            self::ORDER_BARGAIN_FRIEND_JOIN => '好友帮忙砍价',
            self::ORDER_BARGAIN_ACCOMPLISH => '砍价成功',
            self::ORDER_BARGAIN_CLOSE => '砍价关闭',
            self::ORDER_VIRTUAL => '核销码',
            self::COUPON_GIVE => '收到优惠券',
            self::COUPON_CLOSE => '优惠券失效',
            self::COMMISSION => '佣金到账',
            self::INVOICE_APPLY => '申请发票',
            self::OPINION_CREATE => '意见反馈',
        ];
    }

    /**
     * @return array
     */
    public static function getMemberMap(): array
    {
        return [
            self::ORDER_CONSIGN => '订单发货',
            // self::ORDER_RECEIVING => '商家已接单',
            // self::ORDER_CABALLERO_RECEIVING => '骑手已接单',
            // self::ORDER_CABALLERO_CANCEL => '骑手已取消',
            self::ORDER_STOCK_UP_ACCOMPLISH => '备货完成',
            self::ORDER_RETURN_MEMBER_DELIVER => '填写退/换货物流单号',
            self::ORDER_RETURN_APPLY_CLOSE => '退款/退货商家取消',
            self::ORDER_RETURN_MONEY => '收到订单退款',
            self::ORDER_CANCEL => '商家退单',
            self::ORDER_WHOLESALE_OPEN => '拼团开团成功',
            self::ORDER_WHOLESALE_JOIN => '拼团参团成功',
            self::ORDER_WHOLESALE_ACCOMPLISH => '拼团成功',
            self::ORDER_WHOLESALE_CLOSE => '拼团失败',
            self::ORDER_BARGAIN_FRIEND_JOIN => '好友帮忙砍价',
            self::ORDER_BARGAIN_ACCOMPLISH => '砍价成功',
            self::ORDER_BARGAIN_CLOSE => '砍价关闭',
            self::ORDER_VIRTUAL => '核销码',
            self::COUPON_GIVE => '收到优惠券',
            // self::COUPON_CLOSE => '优惠券失效',
            // self::COMMISSION => '佣金到账',
        ];
    }

    /**
     * @return array
     */
    public static function getManagerMap(): array
    {
        return [
            self::ORDER_PAY => '新订单',
            self::ORDER_AFTER_SALE_APPLY => '订单申请退款',
            self::INVOICE_APPLY => '申请发票',
            self::OPINION_CREATE => '意见反馈',
        ];
    }

    /**
     * 默认值
     *
     * @param $action
     * @return array|string[]
     */
    public static function default($action)
    {
        $data = [
            self::ORDER_CONSIGN => [
                'title' => '您的订单发货了',
                'content' => '亲，你的订单已发货，订单号:{order.order_sn}',
                'tables' => [
                    [
                        'title' => '订单',
                        'prefix' => 'order',
                        'tableName' => Order::tableName(),
                        'filterFields' => [
                            'feedback_status',
                            'is_evaluate',
                            'auto_sign_time',
                            'auto_finish_time',
                            'auto_evaluate_time',
                            'auto_sign_time',
                            'is_print',
                            'refund_money',
                            'refund_num',
                            'is_after_sale',
                            'status',
                        ],
                    ],
                ],
            ],
            self::ORDER_RECEIVING => [
                'title' => '商家已接单',
                'content' => '商家已接单, 订单号: {order.order_sn}',
                'tables' => [
                    [
                        'title' => '订单',
                        'prefix' => 'order',
                        'tableName' => Order::tableName(),
                        'filterFields' => [
                            'feedback_status',
                            'is_evaluate',
                            'auto_sign_time',
                            'auto_finish_time',
                            'auto_evaluate_time',
                            'auto_sign_time',
                            'is_print',
                            'refund_money',
                            'refund_num',
                            'is_after_sale',
                            'status',
                        ],
                    ],
                ],
            ],
            self::ORDER_CANCEL => [
                'title' => '商家已退单',
                'content' => '你的订单 {order.order_sn} 已被商家取消',
                'tables' => [
                    [
                        'title' => '订单',
                        'prefix' => 'order',
                        'tableName' => Order::tableName(),
                        'filterFields' => [
                            'feedback_status',
                            'is_evaluate',
                            'auto_sign_time',
                            'auto_finish_time',
                            'auto_evaluate_time',
                            'auto_sign_time',
                            'is_print',
                            'refund_money',
                            'refund_num',
                            'is_after_sale',
                            'status',
                        ],
                    ],
                ],
            ],
            self::ORDER_CABALLERO_RECEIVING => [
                'title' => '已被骑手接单',
                'content' => '你的订单已被骑手接单，订单号:{order.order_sn}',
                'tables' => [
                    [
                        'title' => '订单',
                        'prefix' => 'order',
                        'tableName' => Order::tableName(),
                        'filterFields' => [
                            'feedback_status',
                            'is_evaluate',
                            'auto_sign_time',
                            'auto_finish_time',
                            'auto_evaluate_time',
                            'auto_sign_time',
                            'is_print',
                            'refund_money',
                            'refund_num',
                            'is_after_sale',
                            'status',
                        ],
                    ],
                ],
            ],
            self::ORDER_CABALLERO_CANCEL => [
                'title' => '已被骑手取消',
                'content' => '你的订单{order.order_sn}已被骑手取消，系统正在重新派单中',
                'tables' => [
                    [
                        'title' => '订单',
                        'prefix' => 'order',
                        'tableName' => Order::tableName(),
                        'filterFields' => [
                            'feedback_status',
                            'is_evaluate',
                            'auto_sign_time',
                            'auto_finish_time',
                            'auto_evaluate_time',
                            'auto_sign_time',
                            'is_print',
                            'refund_money',
                            'refund_num',
                            'is_after_sale',
                            'status',
                        ],
                    ],
                ],
            ],
            self::ORDER_PAY => [
                'title' => '您有一笔新订单',
                'content' => '您有一笔新订单 {order.order_sn} 请及时处理',
                'tables' => [
                    [
                        'title' => '订单',
                        'prefix' => 'order',
                        'tableName' => Order::tableName(),
                        'filterFields' => [
                            'feedback_status',
                            'is_evaluate',
                            'auto_sign_time',
                            'auto_finish_time',
                            'auto_evaluate_time',
                            'auto_sign_time',
                            'is_print',
                            'refund_money',
                            'refund_num',
                            'is_after_sale',
                            'status',
                        ],
                    ],
                ],
            ],
            self::ORDER_STOCK_UP_ACCOMPLISH => [
                'title' => '备货完成啦',
                'content' => '亲，你的订单 {order.order_sn} 已备货完成，请在规定时间内完成支付',
                'tables' => [
                    [
                        'title' => '订单',
                        'prefix' => 'order',
                        'tableName' => Order::tableName(),
                        'filterFields' => [
                            'feedback_status',
                            'is_evaluate',
                            'auto_sign_time',
                            'auto_finish_time',
                            'auto_evaluate_time',
                            'auto_sign_time',
                            'is_print',
                            'refund_money',
                            'refund_num',
                            'is_after_sale',
                            'status',
                        ],
                    ],
                ],
            ],
            self::ORDER_AFTER_SALE_APPLY => [
                'title' => '您有一笔订单进行退款申请',
                'content' => '你有一笔订单{order.order_sn}正在申请退款，请及时处理',
                'tables' => [
                    [
                        'title' => '订单',
                        'prefix' => 'order',
                        'tableName' => Order::tableName(),
                        'filterFields' => [
                            'feedback_status',
                            'is_evaluate',
                            'auto_sign_time',
                            'auto_finish_time',
                            'auto_evaluate_time',
                            'auto_sign_time',
                            'is_print',
                            'refund_money',
                            'refund_num',
                            'is_after_sale',
                            'status',
                        ],
                    ],
                ],
            ],
            self::ORDER_RETURN_MEMBER_DELIVER => [
                'title' => '请填写退/换货物流单号',
                'content' => '您的订单 {afterSafe.order_sn} 中的商品，请填写退/换货物流单号。',
                'tables' => [
                    [
                        'title' => '售后申请',
                        'prefix' => 'afterSale',
                        'tableName' => AfterSale::tableName(),
                        'filterFields' => [
                            'status',
                        ],
                    ],
                    [
                        'title' => '订单商品',
                        'prefix' => 'orderProduct',
                        'tableName' => OrderProduct::tableName(),
                        'filterFields' => [
                            'is_evaluate',
                            'gift_flag',
                            'after_sale_id',
                            'is_commission',
                            'status',
                        ],
                    ],
                ],
            ],
            self::ORDER_RETURN_MONEY => [
                'title' => '收到一笔订单退款',
                'content' => '您的订单 {afterSale.order_sn} 中的商品，产生了退款共计 {orderProduct.refund_balance_money} 元, 请在 7 个工作日内注意查收。',
                'tables' => [
                    [
                        'title' => '售后申请',
                        'prefix' => 'afterSale',
                        'tableName' => AfterSale::tableName(),
                        'filterFields' => [
                            'status',
                        ],
                    ],
                    [
                        'title' => '订单商品',
                        'prefix' => 'orderProduct',
                        'tableName' => OrderProduct::tableName(),
                        'filterFields' => [
                            'is_evaluate',
                            'gift_flag',
                            'after_sale_id',
                            'is_commission',
                            'status',
                        ],
                    ],
                ],
            ],
            self::ORDER_RETURN_APPLY_CLOSE => [
                'title' => '有一笔订单退款/退货申请被取消',
                'content' => '您的订单 {afterSale.order_sn} 中的商品申请退款已被商家取消，请去售后查看详情。',
                'tables' => [
                    [
                        'title' => '售后申请',
                        'prefix' => 'afterSale',
                        'tableName' => AfterSale::tableName(),
                        'filterFields' => [
                            'status',
                        ],
                    ],
                    [
                        'title' => '订单商品',
                        'prefix' => 'orderProduct',
                        'tableName' => OrderProduct::tableName(),
                        'filterFields' => [
                            'is_evaluate',
                            'gift_flag',
                            'after_sale_id',
                            'is_commission',
                            'status',
                        ],
                    ],
                ],
            ],
            self::ORDER_WHOLESALE_OPEN => [
                'title' => '拼团开团成功啦',
                'content' => '恭喜您开团成功，分享给好友参团成团更快',
                'tables' => [
                    [
                        'title' => '订单',
                        'prefix' => 'order',
                        'tableName' => Order::tableName(),
                        'filterFields' => [
                            'feedback_status',
                            'is_evaluate',
                            'auto_sign_time',
                            'auto_finish_time',
                            'auto_evaluate_time',
                            'auto_sign_time',
                            'is_print',
                            'refund_money',
                            'refund_num',
                            'is_after_sale',
                            'status',
                        ],
                    ],
                ],
            ],
            self::ORDER_WHOLESALE_JOIN => [
                'title' => '拼团参团成功啦',
                'content' => '恭喜您参团成功，分享给好友参团成团更快',
                'tables' => [
                    [
                        'title' => '订单',
                        'prefix' => 'order',
                        'tableName' => Order::tableName(),
                        'filterFields' => [
                            'feedback_status',
                            'is_evaluate',
                            'auto_sign_time',
                            'auto_finish_time',
                            'auto_evaluate_time',
                            'auto_sign_time',
                            'is_print',
                            'refund_money',
                            'refund_num',
                            'is_after_sale',
                            'status',
                        ],
                    ],
                ],
            ],
            self::ORDER_WHOLESALE_ACCOMPLISH => [
                'title' => '您的拼团成功了',
                'content' => '恭喜您，您有一笔拼团订单已拼团成功，商家将尽快为您发货',
                'tables' => [
                    [
                        'title' => '订单',
                        'prefix' => 'order',
                        'tableName' => Order::tableName(),
                        'filterFields' => [
                            'feedback_status',
                            'is_evaluate',
                            'auto_sign_time',
                            'auto_finish_time',
                            'auto_evaluate_time',
                            'auto_sign_time',
                            'is_print',
                            'refund_money',
                            'refund_num',
                            'is_after_sale',
                            'status',
                        ],
                    ],
                ],
            ],
            self::ORDER_WHOLESALE_CLOSE => [
                'title' => '拼团因人数不足被关闭了',
                'content' => '您参加的拼团活动因人数不足拼团失败，退款将在7个工作日内原路返回至您的支付账号，请注意查收！',
                'tables' => [

                ],
            ],
            self::ORDER_VIRTUAL => [
                'title' => '核销码',
                'content' => '快来点击查看具体信息！',
                'tables' => [

                ],
            ],
            self::COUPON_GIVE => [
                'title' => '收到一张优惠券',
                'content' => '您收到了一张{couponType.type}优惠券',
                'tables' => [
                    [
                        'title' => '优惠券',
                        'prefix' => 'couponType',
                        'tableName' => CouponType::tableName(),
                    ],
                ],
            ],
            self::COUPON_CLOSE => [
                'title' => '优惠券有效期已过',
                'content' => '',
                'tables' => [
                    [
                        'title' => '优惠券',
                        'prefix' => 'couponType',
                        'tableName' => CouponType::tableName(),
                    ],
                ],
            ],
            self::COMMISSION => [
                'title' => '佣金到账了',
                'content' => '',
                'tables' => [

                ],
            ],
            self::OPINION_CREATE => [
                'title' => '意见反馈',
                'content' => '有人提了一个反馈消息',
                'tables' => [

                ],
            ],
            self::INVOICE_APPLY => [
                'title' => '发票申请',
                'content' => '有一个新的发票申请，请及时处理',
                'tables' => [

                ],
            ],
        ];

        return $data[$action] ?? [];
    }
}
