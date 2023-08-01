<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;
use common\helpers\Html;

/**
 * Class OrderAfterSaleTypeEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class OrderAfterSaleTypeEnum extends BaseEnum
{
    const IN_SALE = 1;
    const AFTER_SALE = 2;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::IN_SALE => '售中',
            self::AFTER_SALE => '售后'
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
            self::IN_SALE => Html::tag('span', self::getValue(self::IN_SALE), [
                'class' => "label label-outline-success label-sm",
            ]),
            self::AFTER_SALE => Html::tag('span', self::getValue(self::AFTER_SALE), [
                'class' => "label label-outline-danger label-sm",
            ]),
        ];

        return $listBut[$status] ?? '';
    }
}
