<?php

namespace addons\TinyShop\common\models\common;

/**
 * Class Nice
 * @package addons\TinyShop\common\models\common
 * @author jianyan74 <751393839@qq.com>
 */
class Nice extends Follow
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%addon_shop_common_nice}}';
    }
}