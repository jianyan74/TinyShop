<?php

return [
    // ----------------------- 默认配置 ----------------------- //

    'config' => [
        // 菜单配置
        'menu' => [
            'location' => 'default', // default:系统顶部菜单;addons:应用中心菜单
            'icon' => 'fa fa-shopping-bag',
        ],
        // 子模块配置
        'modules' => [
            // 公共
            'common' => [
                'class' => 'addons\TinyShop\backend\modules\common\Module',
            ],
            // 基础
            'base' => [
                'class' => 'addons\TinyShop\merchant\modules\base\Module',
            ],
            // 订单
            'order' => [
                'class' => 'addons\TinyShop\merchant\modules\order\Module',
            ],
            // 产品
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

    // ----------------------- 权限配置 ----------------------- //

    'authItem' => [
        [
            'title' => '所有权限',
            'name' => '*',
        ],
    ],

    // ----------------------- 快捷入口 ----------------------- //

    'cover' => [

    ],

    // ----------------------- 菜单配置 ----------------------- //

    'menu' => [
        [
            'title' => '运营中心',
            'route' => 'console/index',
            'icon' => 'fa fa-desktop'
        ],
        [
            'title' => '商品管理',
            'route' => 'product/product/index',
            'icon' => 'fa fa-shopping-basket'
        ],
        [
            'title' => '商品配置',
            'route' => 'productConfig',
            'icon' => 'fa fa-pencil',
            'child' => [
                [
                    'title' => '商品分类',
                    'route' => 'product/cate/index',
                ],
                [
                    'title' => '商品规格',
                    'route' => 'base/spec/index',
                ],
                [
                    'title' => '商品类型',
                    'route' => 'base/attribute/index',
                ],
                [
                    'title' => '商品标签',
                    'route' => 'product/tag/index',
                    'icon' => 'fa fa-tags'
                ],
                [
                    'title' => '商品品牌',
                    'route' => 'product/brand/index',
                    'icon' => 'fa fa-tags'
                ],
                [
                    'title' => '供货商',
                    'route' => 'base/supplier/index',
                ],
            ]
        ],
        [
            'title' => '订单管理',
            'route' => 'order/order/index',
            'icon' => 'fa fa-sticky-note'
        ],
        [
            'title' => '营销管理',
            'route' => 'marketing',
            'icon' => 'fa fa-gift',
            'child' => [
                [
                    'title' => '优惠券',
                    'route' => 'marketing/coupon-type/index',
                ],
                [
                    'title' => '积分抵现',
                    'route' => 'marketing/point-config/index',
                ],
                [
                    'title' => '满额包邮',
                    'route' => 'marketing/full-mail/index',
                ],
                [
                    'title' => '小程序直播',
                    'route' => 'marketing/mini-program-live/index',
                ],
            ]
        ],
        [
            'title' => '商品评价',
            'route' => 'product/evaluate/index',
            'icon' => 'fa fa-star'
        ],
        [
            'title' => '售后服务',
            'route' => 'order/customer/index',
            'icon' => 'fa fa-fire-extinguisher'
        ],
        [
            'title' => '发票管理',
            'route' => 'order/invoice/index',
            'icon' => 'fa fa-newspaper-o'
        ],
        [
            'title' => '物流配送',
            'route' => 'base/express-company/index',
            'icon' => 'fa fa-truck',
        ],
        [
            'title' => '基础运营',
            'route' => 'commonOperating',
            'icon' => 'fa fa-share-alt',
            'child' => [
                [
                    'title' => '公告管理',
                    'route' => 'common/notify-announce/index',
                ],
                [
                    'title' => '意见反馈',
                    'route' => 'common/opinion/index',
                ],
                [
                    'title' => '广告管理',
                    'route' => 'common/adv/index',
                ],
                [
                    'title' => '热门搜索',
                    'route' => 'common/search/index',
                ],
                [
                    'title' => '站点帮助',
                    'route' => 'common/helper/index',
                ],
                [
                    'title' => '站点维护',
                    'route' => 'common/maintenance/index',
                ],
                [
                    'title' => '版权信息',
                    'route' => 'common/copyright/index',
                ],
            ]
        ],
        [
            'title' => '数据统计',
            'route' => 'statistics',
            'icon' => 'fa fa-bar-chart',
            'child' => [
                [
                    'title' => '销售分析',
                    'route' => 'statistics/general/index',
                ],
                [
                    'title' => '商品分析',
                    'route' => 'statistics/product-analyze/index',
                ],
                [
                    'title' => '热卖商品',
                    'route' => 'statistics/product-hot/index',
                ],
                [
                    'title' => '交易分析',
                    'route' => 'statistics/transaction-analyze/index',
                ],
            ]
        ],
        [
            'title' => '基础设置',
            'route' => 'setting/display',
            'icon' => 'fa fa fa-gear'
        ],
    ],
];