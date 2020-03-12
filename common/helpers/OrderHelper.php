<?php

namespace addons\TinyShop\common\helpers;

use common\helpers\Html;
use addons\TinyShop\common\enums\RefundStatusEnum;

/**
 * Class OrderHelper
 * @package addons\TinyShop\common\helpers
 * @author jianyan74 <751393839@qq.com>
 */
class OrderHelper
{
    /**
     * 退款操作 - 按钮
     *
     * @param $id
     * @param $status
     * @return string
     */
    public static function refundOperation($id, $status, $urlPrefix = 'product/')
    {
        $data = [
            RefundStatusEnum::APPLY => [
                'name' => RefundStatusEnum::getValue(RefundStatusEnum::APPLY),
                'desc' => '发起了退款申请,等待卖家处理',
                'operation' => [
                    [
                        'name' => '同意',
                        'class' => 'green m-r orderProductAgree'
                    ],
                    [
                        'name' => '拒绝',
                        'class' => 'red orderProductRefuse'
                    ]
                ]
            ],
            RefundStatusEnum::SALES_RETURN => [
                'name' => RefundStatusEnum::getValue(RefundStatusEnum::SALES_RETURN),
                'desc' => '卖家已同意退款申请,等待买家退货',
                'operation' => []
            ],
            RefundStatusEnum::AFFIRM_SALES_RETURN => [
                'name' => RefundStatusEnum::getValue(RefundStatusEnum::AFFIRM_SALES_RETURN),
                'desc' => '买家已退货,等待卖家确认收货',
                'operation' => [
                    [
                        'name' => '确认收货',
                        'class' => 'green m-r orderProductDelivery'
                    ]
                ]
            ],
            RefundStatusEnum::AFFIRM_RETURN_MONEY => [
                'name' => RefundStatusEnum::getValue(RefundStatusEnum::AFFIRM_RETURN_MONEY),
                'desc' => '卖家同意退款',
                'operation' => [
                    [
                        'name' => '确认退款',
                        'class' => 'green m-r'
                    ]
                ]
            ],
            RefundStatusEnum::CONSENT => [
                'name' => RefundStatusEnum::getValue(RefundStatusEnum::CONSENT),
                'desc' => '卖家退款给买家，本次维权结束',
                'operation' => []
            ],
            RefundStatusEnum::NO_PASS_ALWAYS => [
                'name' => RefundStatusEnum::getValue(RefundStatusEnum::NO_PASS_ALWAYS),
                'desc' => '卖家拒绝本次退款，本次维权结束',
                'operation' => []
            ],
            RefundStatusEnum::CANCEL => [
                'name' => RefundStatusEnum::getValue(RefundStatusEnum::CANCEL),
                'desc' => '主动撤销退款，退款关闭',
                'operation' => []
            ],
            RefundStatusEnum::NO_PASS => [
                'name' => RefundStatusEnum::getValue(RefundStatusEnum::NO_PASS),
                'desc' => '拒绝了本次退款申请,等待买家修改',
                'operation' => []
            ]
        ];

        $html = '';
        if (isset($data[$status])) {
            $input = $data[$status];

            $html .= '<div style="text-align: center" class="p-xxs">';
            $html .= "<small id='$id'>";
            $html .= Html::a($input['name'], [$urlPrefix . 'refund-detail', 'id' => $id], [
                'class' => 'cyan'
            ]);
            $html .= "</br>";
            // 其他按钮
            foreach ($input['operation'] as $item) {
                // 确认退款
                if ($status == RefundStatusEnum::AFFIRM_RETURN_MONEY) {
                    $html .= Html::linkButton([$urlPrefix . 'refund-return-money', 'id' => $id], $item['name'], [
                        'class' => $item['class'],
                        'data-toggle' => 'modal',
                        'data-target' => '#ajaxModal',
                    ]);
                } else {
                    $html .= "<a href='javascript:void (0);' class='{$item['class']}'>{$item['name']}</a>";
                }
            }

            $html .= '</small>';
            $html .= '</div>';
        }

        return $html;
    }
}