<?php

namespace addons\TinyShop;

use Yii;
use common\components\Migration;
use common\interfaces\AddonWidget;

/**
 * 升级数据库
 *
 * Class Upgrade
 * @package addons\TinyShop
 */
class Upgrade extends Migration implements AddonWidget
{
    /**
     * @var array
     */
    public $versions = [
        '3.0.0', // 默认版本
    ];

    /**
     * @param $addon
     * @return mixed|void
     * @throws \yii\db\Exception
     */
    public function run($addon)
    {
        switch ($addon->version) {
            case '3.0.0' :
                break;
        }
    }
}
