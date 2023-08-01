<?php

namespace addons\TinyShop\common\enums;

use yii\helpers\Html;
use common\enums\BaseEnum;

/**
 * Class OrderInvoiceAuditStatusEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class OrderInvoiceAuditStatusEnum extends BaseEnum
{
    const ENABLED = 1;
    const DISABLED = 0;
    const DELETE = -1;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::DISABLED => '待开具',
            self::ENABLED => '已开具',
            self::DELETE => '已关闭',
        ];
    }

    /**
     * @param $key
     * @return mixed|string
     */
    public static function html($key)
    {
        $html = [
            self::ENABLED => Html::tag('span', self::getValue(self::ENABLED), array_merge(
                [
                    'class' => "label label-outline-success",
                ]
            )),
            self::DISABLED => Html::tag('span', self::getValue(self::DISABLED), array_merge(
                [
                    'class' => "label label-outline-default",
                ]
            )),
            self::DELETE => Html::tag('span', self::getValue(self::DELETE), array_merge(
                [
                    'class' => "label label-outline-warning",
                ]
            )),
        ];

        return $html[$key] ?? '';
    }
}
