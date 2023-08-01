<?php

namespace addons\TinyShop\common\enums\product;

use yii\helpers\Html;
use common\enums\BaseEnum;

/**
 * Class AuditStatusEnum
 * @package addons\TinyShop\common\enums\product
 * @author jianyan74 <751393839@qq.com>
 */
class AuditStatusEnum extends BaseEnum
{
    const ENABLED = 1;
    const DISABLED = 0;
    const DELETE = -1;
    const GET_OUT_OF_LINE = -10;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::ENABLED => '已通过',
            self::DISABLED => '审核中',
            self::DELETE => '已拒绝',
            self::GET_OUT_OF_LINE => '违规下架',
        ];
    }

    /**
     * @return array
     */
    public static function audit(): array
    {
        return [
            self::DISABLED => '审核中',
            self::DELETE => '已拒绝',
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
