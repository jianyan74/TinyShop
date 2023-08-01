<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;
use common\helpers\Html;

/**
 * 退换货申请
 *
 * Class RefundTypeEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class RefundTypeEnum extends BaseEnum
{
    const MONEY = 1;
    const MONEY_AND_PRODUCT = 2;
    const EXCHANGE_PRODUCT = 3;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::MONEY => '仅退款',
            self::MONEY_AND_PRODUCT => '退款且退货',
            self::EXCHANGE_PRODUCT => '换货',
        ];
    }

    /**
     * 是否标签
     *
     * @param int $status
     * @return mixed
     */
    public static function html(int $status)
    {
        $listBut = [
            self::MONEY => Html::tag('span', self::getValue(self::MONEY), [
                'class' => "label label-outline-info label-sm",
            ]),
            self::MONEY_AND_PRODUCT => Html::tag('span', self::getValue(self::MONEY_AND_PRODUCT), [
                'class' => "label label-outline-primary label-sm",
            ]),
            self::EXCHANGE_PRODUCT => Html::tag('span', self::getValue(self::EXCHANGE_PRODUCT), [
                'class' => "label label-outline-purple label-sm",
            ]),
        ];

        return $listBut[$status] ?? '';
    }
}
