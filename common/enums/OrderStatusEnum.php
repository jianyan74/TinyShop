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
    const PAY = 1;
    const SHIPMENTS = 2;
    const SING = 3;
    const ACCOMPLISH = 4;
    const RETUREN_APPLY = -1;
    const RETUREN_ING = -2;
    const RETUREN = -3;
    const REPEAL = -4;
    const REPEAL_APPLY = -5;
    const WHOLESALE = 101;
    const SUBSCRIPTION_PAY = 201;
    const STOCK_UP = 202;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::NOT_PAY => '待付款',
            // 拼团
            // self::WHOLESALE => '待成团',
            self::PAY => '待发货', // 已付款
            self::SHIPMENTS => '已发货',
            self::SING => '已收货',
            self::ACCOMPLISH => '已完成',
            self::REPEAL => '已关闭',
            // self::RETUREN_APPLY => '退货申请',
            self::RETUREN_ING => '退款中',
            // self::RETUREN => '已退货',
            // self::REPEAL_APPLY => '撤销申请',
            // 预售
            // self::SUBSCRIPTION_PAY => '订金已支付',
            // self::STOCK_UP => '备货中',
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
            self::RETUREN_APPLY => '退货申请',
            self::RETUREN_ING => '退款中',
            self::RETUREN => '已退货',
            self::REPEAL_APPLY => '撤销申请',
        ]);
    }

    public static function common()
    {

    }


    /**
     * 获取实物订单所有可能的订单状态
     */
    public static function getOrderCommonStatus()
    {
        $status = [
            [
                'status_id' => '0',
                'name' => '待付款',
                'is_refund' => 0, // 是否可以申请退款
                'operation' => [
                    '0' => [
                        'no' => 'pay',
                        'name' => '线下支付',
                        'color' => '#FF9800'
                    ],
                    '1' => [
                        'no' => 'close',
                        'color' => '#E61D1D',
                        'name' => '交易关闭'
                    ],
                    '2' => [
                        'no' => 'adjust_price',
                        'color' => '#4CAF50',
                        'name' => '修改价格'
                    ],
                    '3' => [
                        'no' => 'seller_memo',
                        'color' => '#666666',
                        'name' => '备注'
                    ]
                ],
                'member_operation' => [
                    '0' => [
                        'no' => 'pay',
                        'name' => '去支付',
                        'color' => '#F15050'
                    ],

                    '1' => [
                        'no' => 'close',
                        'name' => '关闭订单',
                        'color' => '#999999'
                    ]
                ]
            ],
            [
                'status_id' => '1',
                'name' => '待发货',
                'is_refund' => 1,
                'operation' => [
                    '0' => [
                        'no' => 'delivery',
                        'color' => 'green',
                        'name' => '发货'
                    ],
                    '1' => [
                        'no' => 'seller_memo',
                        'color' => '#666666',
                        'name' => '备注'
                    ],
                    '2' => [
                        'no' => 'update_address',
                        'color' => '#51A351',
                        'name' => '修改地址'
                    ]
                ],
                'member_operation' => []
            ],
            [
                'status_id' => '2',
                'name' => '已发货',
                'is_refund' => 1,
                'operation' => [
                    '0' => [
                        'no' => 'seller_memo',
                        'color' => '#666666',
                        'name' => '备注'
                    ],
                    '1' => [
                        'no' => 'logistics',
                        'color' => '#666666',
                        'name' => '查看物流'
                    ],
                    '2' => [
                        'no' => 'getdelivery',
                        'name' => '确认收货',
                        'color' => '#FF6600'
                    ]
                ],

                'member_operation' => [
                    '0' => [
                        'no' => 'getdelivery',
                        'name' => '确认收货',
                        'color' => '#FF6600'
                    ],
                    '1' => [
                        'no' => 'logistics',
                        'color' => '#cccccc',
                        'name' => '查看物流'
                    ]
                ]
            ],
            [
                'status_id' => '3',
                'name' => '已收货',
                'is_refund' => 0,
                'operation' => [
                    '0' => [
                        'no' => 'seller_memo',
                        'color' => '#666666',
                        'name' => '备注'
                    ],
                    '1' => [
                        'no' => 'logistics',
                        'color' => '#666666',
                        'name' => '查看物流'
                    ]
                ],
                'member_operation' => [
                    '0' => [
                        'no' => 'logistics',
                        'color' => '#cccccc',
                        'name' => '查看物流'
                    ]
                ]
            ],
            [
                'status_id' => '4',
                'name' => '已完成',
                'is_refund' => 0,
                'operation' => [
                    '0' => [
                        'no' => 'seller_memo',
                        'color' => '#666666',
                        'name' => '备注'
                    ],
                    '1' => [
                        'no' => 'logistics',
                        'color' => '#666666',
                        'name' => '查看物流'
                    ]
                ],
                'member_operation' => [
                    '0' => [
                        'no' => 'logistics',
                        'color' => '#cccccc',
                        'name' => '查看物流'
                    ]
                ]
            ],
            [
                'status_id' => '5',
                'name' => '已关闭',
                'is_refund' => 0,
                'operation' => [
                    '0' => [
                        'no' => 'seller_memo',
                        'color' => '#666666',
                        'name' => '备注'
                    ],
                    '1' => [
                        'no' => 'delete_order',
                        'color' => '#ff0000',
                        'name' => '删除订单'
                    ]
                ],
                'member_operation' => [
                    '0' => [
                        'no' => 'delete_order',
                        'color' => '#ff0000',
                        'name' => '删除订单'
                    ]
                ]
            ],
            [
                'status_id' => '-1',
                'name' => '退款中',
                'is_refund' => 1,
                'operation' => [
                    '0' => [
                        'no' => 'seller_memo',
                        'color' => '#666666',
                        'name' => '备注'
                    ]
                ],
                'member_operation' => []
            ]
        ];
        return $status;
    }

    /**
     * 获取实物订单所有可能的订单状态
     */
    public static function getOrderO2oStatus()
    {
        $status = [
            [
                'status_id' => '0',
                'name' => '待付款',
                'is_refund' => 0, // 是否可以申请退款
                'operation' => [
                    '0' => [
                        'no' => 'pay',
                        'name' => '线下支付',
                        'color' => '#FF9800'
                    ],
                    '1' => [
                        'no' => 'close',
                        'color' => '#E61D1D',
                        'name' => '交易关闭'
                    ],
                    '2' => [
                        'no' => 'adjust_price',
                        'color' => '#4CAF50',
                        'name' => '修改价格'
                    ],
                    '3' => [
                        'no' => 'seller_memo',
                        'color' => '#666666',
                        'name' => '备注'
                    ]
                ],
                'member_operation' => [
                    '0' => [
                        'no' => 'pay',
                        'name' => '去支付',
                        'color' => '#F15050'
                    ],

                    '1' => [
                        'no' => 'close',
                        'name' => '关闭订单',
                        'color' => '#999999'
                    ]
                ]
            ],
            [
                'status_id' => '1',
                'name' => '待发货',
                'is_refund' => 1,
                'operation' => [
                    '0' => [
                        'no' => 'o2o_delivery',
                        'color' => 'green',
                        'name' => '发货'
                    ],
                    '1' => [
                        'no' => 'seller_memo',
                        'color' => '#666666',
                        'name' => '备注'
                    ],
                    '2' => [
                        'no' => 'update_address',
                        'color' => '#51A351',
                        'name' => '修改地址'
                    ]
                ],
                'member_operation' => []
            ],
            [
                'status_id' => '2',
                'name' => '已发货',
                'is_refund' => 1,
                'operation' => [
                    '0' => [
                        'no' => 'seller_memo',
                        'color' => '#666666',
                        'name' => '备注'
                    ],
                    '1' => [
                        'no' => 'logistics',
                        'color' => '#666666',
                        'name' => '查看物流'
                    ],
                    '2' => [
                        'no' => 'getdelivery',
                        'name' => '确认收货',
                        'color' => '#FF6600'
                    ]
                ],

                'member_operation' => [
                    '0' => [
                        'no' => 'getdelivery',
                        'name' => '确认收货',
                        'color' => '#FF6600'
                    ],
                    '1' => [
                        'no' => 'logistics',
                        'color' => '#cccccc',
                        'name' => '查看物流'
                    ]
                ]
            ],
            [
                'status_id' => '3',
                'name' => '已收货',
                'is_refund' => 0,
                'operation' => [
                    '0' => [
                        'no' => 'seller_memo',
                        'color' => '#666666',
                        'name' => '备注'
                    ],
                    '1' => [
                        'no' => 'logistics',
                        'color' => '#666666',
                        'name' => '查看物流'
                    ]
                ],
                'member_operation' => [
                    '0' => [
                        'no' => 'logistics',
                        'color' => '#cccccc',
                        'name' => '查看物流'
                    ]
                ]
            ],
            [
                'status_id' => '4',
                'name' => '已完成',
                'is_refund' => 0,
                'operation' => [
                    '0' => [
                        'no' => 'seller_memo',
                        'color' => '#666666',
                        'name' => '备注'
                    ],
                    '1' => [
                        'no' => 'logistics',
                        'color' => '#666666',
                        'name' => '查看物流'
                    ]
                ],
                'member_operation' => [
                    '0' => [
                        'no' => 'logistics',
                        'color' => '#cccccc',
                        'name' => '查看物流'
                    ]
                ]
            ],
            [
                'status_id' => '5',
                'name' => '已关闭',
                'is_refund' => 0,
                'operation' => [
                    '0' => [
                        'no' => 'seller_memo',
                        'color' => '#666666',
                        'name' => '备注'
                    ],
                    '1' => [
                        'no' => 'delete_order',
                        'color' => '#ff0000',
                        'name' => '删除订单'
                    ]
                ],
                'member_operation' => [
                    '0' => [
                        'no' => 'delete_order',
                        'color' => '#ff0000',
                        'name' => '删除订单'
                    ]
                ]
            ],
            [
                'status_id' => '-1',
                'name' => '退款中',
                'is_refund' => 1,
                'operation' => [
                    '0' => [
                        'no' => 'seller_memo',
                        'color' => '#666666',
                        'name' => '备注'
                    ]
                ],
                'member_operation' => []
            ]
        ];
        return $status;
    }

    /**
     * 获取虚拟订单所有可能的订单状态
     */
    public static function getVirtualOrderCommonStatus()
    {
        $status = [
            [
                'status_id' => '0',
                'name' => '待付款',
                'is_refund' => 0, // 是否可以申请退款
                'operation' => [
                    '0' => [
                        'no' => 'pay',
                        'name' => '线下支付',
                        'color' => '#FF9800'
                    ],
                    '1' => [
                        'no' => 'close',
                        'color' => '#E61D1D',
                        'name' => '交易关闭'
                    ],
                    '2' => [
                        'no' => 'adjust_price',
                        'color' => '#4CAF50',
                        'name' => '修改价格'
                    ],
                    '3' => [
                        'no' => 'seller_memo',
                        'color' => '#666666',
                        'name' => '备注'
                    ]
                ],
                'member_operation' => [
                    '0' => [
                        'no' => 'pay',
                        'name' => '去支付',
                        'color' => '#F15050'
                    ],

                    '1' => [
                        'no' => 'close',
                        'name' => '关闭订单',
                        'color' => '#999999'
                    ]
                ]
            ],
            [
                'status_id' => '6',
                'name' => '已付款',
                'is_refund' => 0,
                'operation' => [
                    '0' => [
                        'no' => 'seller_memo',
                        'color' => '#666666',
                        'name' => '备注'
                    ]
                ],
                'member_operation' => []
            ],
            [
                'status_id' => '4',
                'name' => '已完成',
                'is_refund' => 0,
                'operation' => [
                    '0' => [
                        'no' => 'seller_memo',
                        'color' => '#666666',
                        'name' => '备注'
                    ]
                ],
                'member_operation' => []
            ],
            [
                'status_id' => '5',
                'name' => '已关闭',
                'is_refund' => 0,
                'operation' => [
                    '0' => [
                        'no' => 'seller_memo',
                        'color' => '#666666',
                        'name' => '备注'
                    ],
                    '1' => [
                        'no' => 'delete_order',
                        'color' => '#ff0000',
                        'name' => '删除订单'
                    ]
                ],
                'member_operation' => [
                    '0' => [
                        'no' => 'delete_order',
                        'color' => '#ff0000',
                        'name' => '删除订单'
                    ]
                ]
            ]
        ];
        return $status;
    }

    /**
     * 获取自提订单相关状态
     */
    public static function getSinceOrderStatus()
    {
        $status = [
            [
                'status_id' => '0',
                'name' => '待付款',
                'is_refund' => 0, // 是否可以申请退款
                'operation' => [
                    '0' => [
                        'no' => 'pay',
                        'name' => '线下支付',
                        'color' => '#FF9800'
                    ],
                    '1' => [
                        'no' => 'close',
                        'color' => '#E61D1D',
                        'name' => '交易关闭'
                    ],
                    '2' => [
                        'no' => 'adjust_price',
                        'color' => '#4CAF50',
                        'name' => '修改价格'
                    ],
                    '3' => [
                        'no' => 'seller_memo',
                        'color' => '#666666',
                        'name' => '备注'
                    ]
                ],
                'member_operation' => [
                    '0' => [
                        'no' => 'pay',
                        'name' => '去支付',
                        'color' => '#F15050'
                    ],

                    '1' => [
                        'no' => 'close',
                        'name' => '关闭订单',
                        'color' => '#999999'
                    ]
                ]
            ],
            [
                'status_id' => '1',
                'name' => '待提货',
                'is_refund' => 1,
                'operation' => [
                    '0' => [
                        'no' => 'pickup',
                        'color' => '#FF9800',
                        'name' => '提货'
                    ],
                    '1' => [
                        'no' => 'seller_memo',
                        'color' => '#666666',
                        'name' => '备注'
                    ]
                ],
                'member_operation' => [
                    '0' => [
                        'no' => 'member_pickup',
                        'color' => '#FF9800',
                        'name' => '提货'
                    ]
                ]
            ],
            [
                'status_id' => '3',
                'name' => '已提货',
                'is_refund' => 0,
                'operation' => [

                    '0' => [
                        'no' => 'seller_memo',
                        'color' => '#666666',
                        'name' => '备注'
                    ],
                    '1' => [
                        'no' => 'logistics',
                        'color' => '#51A351',
                        'name' => '查看物流'
                    ]
                ],

                'member_operation' => []
            ],
            [
                'status_id' => '4',
                'name' => '已完成',
                'is_refund' => 0,
                'operation' => [
                    '0' => [
                        'no' => 'seller_memo',
                        'color' => '#666666',
                        'name' => '备注'
                    ],
                    '1' => [
                        'no' => 'logistics',
                        'color' => '#51A351',
                        'name' => '查看物流'
                    ]
                ],

                'member_operation' => []
            ],
            [
                'status_id' => '5',
                'name' => '已关闭',
                'is_refund' => 0,
                'operation' => [
                    '0' => [
                        'no' => 'seller_memo',
                        'color' => '#666666',
                        'name' => '备注'
                    ],
                    '1' => [
                        'no' => 'delete_order',
                        'color' => '#ff0000',
                        'name' => '删除订单'
                    ]
                ],

                'member_operation' => [
                    '0' => [
                        'no' => 'delete_order',
                        'color' => '#ff0000',
                        'name' => '删除订单'
                    ]
                ]
            ],
            [
                'status_id' => '-1',
                'name' => '退款中',
                'is_refund' => 1,
                'operation' => [
                    '0' => [
                        'no' => 'seller_memo',
                        'color' => '#666666',
                        'name' => '备注'
                    ]
                ],
                'member_operation' => []
            ]
        ];
        return $status;
    }


    /**
     * 拼团订单状态
     *
     * @return array
     */
    public static function getOrderPintuanStatus()
    {
        $status = [
            [
                'status_id' => '0',
                'name' => '待付款',
                'is_refund' => 0, // 是否可以申请退款
                'operation' => [
                    '0' => [
                        'no' => 'pay',
                        'name' => '线下支付',
                        'color' => '#FF9800'
                    ],
                    '1' => [
                        'no' => 'close',
                        'color' => '#E61D1D',
                        'name' => '交易关闭'
                    ],
                    '2' => [
                        'no' => 'adjust_price',
                        'color' => '#4CAF50',
                        'name' => '修改价格'
                    ],
                    '3' => [
                        'no' => 'seller_memo',
                        'color' => '#666666',
                        'name' => '备注'
                    ]
                ],
                'member_operation' => [
                    '0' => [
                        'no' => 'pay',
                        'name' => '去支付',
                        'color' => '#F15050'
                    ],

                    '1' => [
                        'no' => 'close',
                        'name' => '关闭订单',
                        'color' => '#999999'
                    ]
                ]
            ],
            [
                'status_id' => '6',
                'name' => '待成团',
                'is_refund' => 1,
                'operation' => [

                    '1' => [
                        'no' => 'seller_memo',
                        'color' => '#666666',
                        'name' => '备注'
                    ],
                    '2' => [
                        'no' => 'update_address',
                        'color' => '#51A351',
                        'name' => '修改地址'
                    ]
                ],
                'member_operation' => []
            ],
            [
                'status_id' => '1',
                'name' => '待发货',
                'is_refund' => 1,
                'operation' => [
                    '0' => [
                        'no' => 'delivery',
                        'color' => 'green',
                        'name' => '发货'
                    ],
                    '1' => [
                        'no' => 'seller_memo',
                        'color' => '#666666',
                        'name' => '备注'
                    ],
                    '2' => [
                        'no' => 'update_address',
                        'color' => '#51A351',
                        'name' => '修改地址'
                    ]
                ],
                'member_operation' => []
            ],
            [
                'status_id' => '2',
                'name' => '已发货',
                'is_refund' => 1,
                'operation' => [
                    '0' => [
                        'no' => 'seller_memo',
                        'color' => '#666666',
                        'name' => '备注'
                    ],
                    '1' => [
                        'no' => 'logistics',
                        'color' => '#666666',
                        'name' => '查看物流'
                    ],
                    '2' => [
                        'no' => 'getdelivery',
                        'name' => '确认收货',
                        'color' => '#FF6600'
                    ]
                ],

                'member_operation' => [
                    '0' => [
                        'no' => 'getdelivery',
                        'name' => '确认收货',
                        'color' => '#FF6600'
                    ],
                    '1' => [
                        'no' => 'logistics',
                        'color' => '#cccccc',
                        'name' => '查看物流'
                    ]
                ]
            ],
            [
                'status_id' => '3',
                'name' => '已收货',
                'is_refund' => 0,
                'operation' => [
                    '0' => [
                        'no' => 'seller_memo',
                        'color' => '#666666',
                        'name' => '备注'
                    ],
                    '1' => [
                        'no' => 'logistics',
                        'color' => '#666666',
                        'name' => '查看物流'
                    ]
                ],
                'member_operation' => [
                    '0' => [
                        'no' => 'logistics',
                        'color' => '#cccccc',
                        'name' => '查看物流'
                    ]
                ]
            ],
            [
                'status_id' => '4',
                'name' => '已完成',
                'is_refund' => 0,
                'operation' => [
                    '0' => [
                        'no' => 'seller_memo',
                        'color' => '#666666',
                        'name' => '备注'
                    ],
                    '1' => [
                        'no' => 'logistics',
                        'color' => '#666666',
                        'name' => '查看物流'
                    ]
                ],
                'member_operation' => [
                    '0' => [
                        'no' => 'logistics',
                        'color' => '#cccccc',
                        'name' => '查看物流'
                    ]
                ]
            ],
            [
                'status_id' => '5',
                'name' => '已关闭',
                'is_refund' => 0,
                'operation' => [
                    '0' => [
                        'no' => 'seller_memo',
                        'color' => '#666666',
                        'name' => '备注'
                    ],
                    '1' => [
                        'no' => 'delete_order',
                        'color' => '#ff0000',
                        'name' => '删除订单'
                    ]
                ],
                'member_operation' => [
                    '0' => [
                        'no' => 'delete_order',
                        'color' => '#ff0000',
                        'name' => '删除订单'
                    ]
                ]
            ],
            [
                'status_id' => '-1',
                'name' => '退款中',
                'is_refund' => 1,
                'operation' => [
                    '0' => [
                        'no' => 'seller_memo',
                        'color' => '#666666',
                        'name' => '备注'
                    ]
                ],
                'member_operation' => []
            ]
        ];
        return $status;
    }

    /**
     * 获取预售操作状态
     */
    public static function getOrderPresellStatus()
    {
        $status = [

            [
                'status_id' => '6',
                'name' => '订金待支付',
                'is_refund' => 0, // 是否可以申请退款
                'operation' => [
                    '0' => [
                        'no' => 'order_presell',
                        'name' => '线下支付',
                        'color' => '#FF9800'
                    ],
                    '1' => [
                        'no' => 'close',
                        'color' => '#E61D1D',
                        'name' => '交易关闭'
                    ],

                ],
                'member_operation' => [
                    '0' => [
                        'no' => 'pay_presell',
                        'name' => '去支付',
                        'color' => '#F15050'
                    ],

                    '1' => [
                        'no' => 'close',
                        'name' => '关闭订单',
                        'color' => '#999999'
                    ]
                ]
            ],
            [
                'status_id' => '7',
                'name' => '备货中',
                'is_refund' => 0, // 是否可以申请退款
                'operation' => [
                    '0' => [
                        'no' => 'stocking_complete',
                        'name' => '备货完成',
                        'color' => '#F15050'
                    ],
                ],
                'member_operation' => [
                ]
            ],
            [
                'status_id' => '0',
                'name' => '预售中',
                'is_refund' => 0, // 是否可以申请退款
                'operation' => [
                    '0' => [
                        'no' => 'pay',
                        'name' => '线下支付',
                        'color' => '#FF9800'
                    ],
                    '1' => [
                        'no' => 'close',
                        'color' => '#E61D1D',
                        'name' => '交易关闭'
                    ],
                    '2' => [
                        'no' => 'adjust_price',
                        'color' => '#4CAF50',
                        'name' => '修改价格'
                    ],
                    '3' => [
                        'no' => 'seller_memo',
                        'color' => '#666666',
                        'name' => '备注'
                    ]
                ],
                'member_operation' => [
                    '0' => [
                        'no' => 'pay',
                        'name' => '去支付',
                        'color' => '#F15050'
                    ],

                    '1' => [
                        'no' => 'close',
                        'name' => '关闭订单',
                        'color' => '#999999'
                    ]
                ]
            ],
            [
                'status_id' => '1',
                'name' => '待发货',
                'is_refund' => 1,
                'operation' => [
                    '0' => [
                        'no' => 'delivery',
                        'color' => 'green',
                        'name' => '发货'
                    ],
                    '1' => [
                        'no' => 'seller_memo',
                        'color' => '#666666',
                        'name' => '备注'
                    ],
                    '2' => [
                        'no' => 'update_address',
                        'color' => '#51A351',
                        'name' => '修改地址'
                    ]
                ],
                'member_operation' => []
            ],
            [
                'status_id' => '2',
                'name' => '已发货',
                'is_refund' => 1,
                'operation' => [
                    '0' => [
                        'no' => 'seller_memo',
                        'color' => '#666666',
                        'name' => '备注'
                    ],
                    '1' => [
                        'no' => 'logistics',
                        'color' => '#666666',
                        'name' => '查看物流'
                    ],
                    '2' => [
                        'no' => 'getdelivery',
                        'name' => '确认收货',
                        'color' => '#FF6600'
                    ]
                ],

                'member_operation' => [
                    '0' => [
                        'no' => 'getdelivery',
                        'name' => '确认收货',
                        'color' => '#FF6600'
                    ],
                    '1' => [
                        'no' => 'logistics',
                        'color' => '#cccccc',
                        'name' => '查看物流'
                    ]
                ]
            ],
            [
                'status_id' => '3',
                'name' => '已收货',
                'is_refund' => 0,
                'operation' => [
                    '0' => [
                        'no' => 'seller_memo',
                        'color' => '#666666',
                        'name' => '备注'
                    ],
                    '1' => [
                        'no' => 'logistics',
                        'color' => '#666666',
                        'name' => '查看物流'
                    ]
                ],
                'member_operation' => [
                    '0' => [
                        'no' => 'logistics',
                        'color' => '#cccccc',
                        'name' => '查看物流'
                    ]
                ]
            ],
            [
                'status_id' => '4',
                'name' => '已完成',
                'is_refund' => 0,
                'operation' => [
                    '0' => [
                        'no' => 'seller_memo',
                        'color' => '#666666',
                        'name' => '备注'
                    ],
                    '1' => [
                        'no' => 'logistics',
                        'color' => '#666666',
                        'name' => '查看物流'
                    ]
                ],
                'member_operation' => [
                    '0' => [
                        'no' => 'logistics',
                        'color' => '#cccccc',
                        'name' => '查看物流'
                    ]
                ]
            ],
            [
                'status_id' => '5',
                'name' => '已关闭',
                'is_refund' => 0,
                'operation' => [
                    '0' => [
                        'no' => 'seller_memo',
                        'color' => '#666666',
                        'name' => '备注'
                    ],
                    '1' => [
                        'no' => 'delete_order',
                        'color' => '#ff0000',
                        'name' => '删除订单'
                    ]
                ],
                'member_operation' => [
                    '0' => [
                        'no' => 'delete_order',
                        'color' => '#ff0000',
                        'name' => '删除订单'
                    ]
                ]
            ],
            [
                'status_id' => '-1',
                'name' => '退款中',
                'is_refund' => 1,
                'operation' => [
                    '0' => [
                        'no' => 'seller_memo',
                        'color' => '#666666',
                        'name' => '备注'
                    ]
                ],
                'member_operation' => []
            ]
        ];
        return $status;
    }

    /**
     * 公用未支付状态
     */
    protected function commonNoPayStatus()
    {
        return [
            'name' => '待付款',
            'is_refund' => 0, // 是否可以申请退款
            'operation' => [
                '0' => [
                    'no' => 'pay',
                    'name' => '线下支付',
                    'color' => '#FF9800'
                ],
                '1' => [
                    'no' => 'close',
                    'color' => '#E61D1D',
                    'name' => '交易关闭'
                ],
                '2' => [
                    'no' => 'adjust_price',
                    'color' => '#4CAF50',
                    'name' => '修改价格'
                ],
                '3' => [
                    'no' => 'seller_memo',
                    'color' => '#666666',
                    'name' => '备注'
                ]
            ],
            'member_operation' => [
                '0' => [
                    'no' => 'pay',
                    'name' => '去支付',
                    'color' => '#F15050'
                ],
                '1' => [
                    'no' => 'close',
                    'name' => '关闭订单',
                    'color' => '#999999'
                ]
            ]
        ];
    }
}