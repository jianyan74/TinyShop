<?php

namespace addons\TinyShop;

use Yii;
use yii\db\ColumnSchemaBuilder;
use yii\db\Migration;
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
        '1.1.0',
        '1.2.0',
    ];

    /**
    * @param $addon
    * @return mixed|void
    * @throws \yii\db\Exception
    */
    public function run($addon)
    {

    }
}