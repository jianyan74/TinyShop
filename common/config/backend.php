<?php

return [

    // ----------------------- 参数配置 ----------------------- //
    'config' => [
        // 菜单配置
        'menu' => [
            'location' => 'default', // default:系统顶部菜单;addons:应用中心菜单
            'icon' => 'fa fa-shopping-bag',
            'sort' => 200, // 自定义排序
            'pattern' => [], // 可见开发模式 b2c、b2b2c、saas 不填默认全部可见, 可设置为 blank 为全部不可见
        ],
        // 子模块配置
        'modules' => [
            // 公共
            'common' => [
                'class' => 'addons\TinyShop\merchant\modules\common\Module',
            ],
            // 订单
            'order' => [
                'class' => 'addons\TinyShop\merchant\modules\order\Module',
            ],
            // 商品
            'product' => [
                'class' => 'addons\TinyShop\merchant\modules\product\Module',
            ],
            // 营销
            'marketing' => [
                'class' => 'addons\TinyShop\merchant\modules\marketing\Module',
            ],
            // 统计
            'statistics' => [
                'class' => 'addons\TinyShop\merchant\modules\statistics\Module',
            ],
        ],
    ],

    // ----------------------- 菜单配置 ----------------------- //

    'menu' => [
        [
            'title' => '运营中心',
            'name' => 'statistics/index/index',
            'icon' => 'fa fa-desktop'
        ],
        [
            'title' => '商品管理',
            'name' => 'product/product/index',
            'icon' => 'fa fa-shopping-basket'
        ],
        [
            'title' => '商品配置',
            'name' => 'productConfig',
            'icon' => 'fa fa-marker',
            'pattern' => ['b2c', 'b2b2c'],
            'child' => [
                [
                    'title' => '商品分类',
                    'name' => 'product/cate/index',
                ],
                [
                    'title' => '商品规格',
                    'name' => 'common/spec/index',
                    'pattern' => ['b2c']
                ],
                [
                    'title' => '商品参数',
                    'name' => 'common/attribute/index',
                    'pattern' => ['b2c']
                ],
                [
                    'title' => '商品标签',
                    'name' => 'product/tag/index',
                    'icon' => 'fa fa-tags',
                    'pattern' => ['b2c']
                ],
                [
                    'title' => '商品品牌',
                    'name' => 'product/brand/index',
                    'icon' => 'fa fa-tags'
                ],
                [
                    'title' => '商品服务',
                    'name' => 'common/product-service/index',
                ],
                [
                    'title' => '售后保障',
                    'name' => 'common/product-after-sale-explain/index',
                ],
                [
                    'title' => '供货商',
                    'name' => 'common/supplier/index',
                    'pattern' => ['b2c']
                ],
            ]
        ],
        [
            'title' => '订单管理',
            'name' => 'order',
            'icon' => 'fa fa-sticky-note',
            'child' => [
                [
                    'title' => '商城订单',
                    'name' => 'order/order/index',
                ],
                [
                    'title' => '充值订单',
                    'name' => 'order/recharge/index',
                    'pattern' => ['b2c', 'b2b2c'],
                ],
                [
                    'title' => '订单评价',
                    'name' => 'order/evaluate/index',
                    'pattern' => ['b2c', 'b2b2c'],
                ],
                [
                    'title' => '售后维权',
                    'name' => 'order/after-sale/index',
                    'pattern' => ['b2c', 'b2b2c'],
                ],
                [
                    'title' => '发票管理',
                    'name' => 'order/invoice/index',
                    'pattern' => ['b2c']
                ],
            ]
        ],
        [
            'title' => '营销管理',
            'name' => 'marketing',
            'icon' => 'fa fa-gift',
            'pattern' => ['b2c', 'b2b2c'],
            'child' => [
                [
                    'title' => '优惠券',
                    'name' => 'marketing/coupon-type/index',
                    'pattern' => ['b2c', 'b2b2c']
                ],
                [
                    'title' => '积分抵现',
                    'name' => 'marketing/point-config/index',
                    'pattern' => ['b2c', 'b2b2c']
                ],
                [
                    'title' => '满额包邮',
                    'name' => 'marketing/full-mail/index',
                    'pattern' => ['b2c']
                ],
                [
                    'title' => '商品海报',
                    'name' => 'marketing/product-poster/index',
                ],
            ]
        ],
        [
            'title' => '会员营销',
            'name' => 'memberMarketing',
            'icon' => 'fa fa-reply-all',
            'pattern' => ['b2c', 'b2b2c'],
            'child' => [
                [
                    'title' => '充值礼包',
                    'name' => 'marketing/recharge-config/index',
                ],
            ]
        ],
        [
            'title' => '物流配送',
            'name' => 'common/express-company/index',
            'icon' => 'fa fa-shipping-fast',
            'pattern' => ['b2c']
        ],
        [
            'title' => '基础运营',
            'name' => 'commonOperating',
            'icon' => 'fa fa-share-alt',
            'pattern' => ['b2c', 'b2b2c'],
            'child' => [
                [
                    'title' => '公告管理',
                    'name' => 'common/notify-announce/index',
                ],
                [
                    'title' => '广告管理',
                    'name' => 'common/adv/index',
                ],
                [
                    'title' => '热门搜索',
                    'name' => 'common/hot-search/index',
                ],
                [
                    'title' => '意见反馈',
                    'name' => 'common/opinion/index',
                ],
                [
                    'title' => '协议配置',
                    'name' => 'common/protocol/index',
                ],
                [
                    'title' => '站点帮助',
                    'name' => 'common/helper/index',
                ],
                [
                    'title' => '站点维护',
                    'name' => 'common/maintenance/index',
                ],
                [
                    'title' => '版权信息',
                    'name' => 'common/copyright/index',
                ],
            ]
        ],
        [
            'title' => '数据统计',
            'name' => 'statistics',
            'icon' => 'fa fa-chart-bar',
            'child' => [
                [
                    'title' => '销售分析',
                    'name' => 'statistics/general/index',
                ],
                [
                    'title' => '商品分析',
                    'name' => 'statistics/product-analyze/index',
                ],
                [
                    'title' => '热卖商品',
                    'name' => 'statistics/product-hot/index',
                ],
                [
                    'title' => '交易分析',
                    'name' => 'statistics/transaction-analyze/index',
                ],
                [
                    'title' => '用户分析',
                    'name' => 'statistics/member/index',
                ],
                [
                    'title' => '搜索分析',
                    'name' => 'statistics/search/index',
                ],
                [
                    'title' => '优惠券分析',
                    'name' => 'statistics/coupon/index',
                ],
            ]
        ],
        [
            'title' => '基础设置',
            'name' => 'setting/display',
            'icon' => 'fa fa-cogs'
        ],
    ],

    // ----------------------- 权限配置 ----------------------- //

    'authItem' => [
        [
            'title' => '所有权限',
            'name' => '*',
        ],
    ],
];
