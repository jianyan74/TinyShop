<?php

namespace addons\TinyShop\backend\assets;

use yii\web\AssetBundle;

/**
 * 静态资源管理
 *
 * Class AppAsset
 * @package addons\TinyShop\backend\assets
 */
class AppAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@addons/TinyShop/merchant/resources/';

    public $css = [
        'css/tinyshop.css',
    ];

    public $js = [
        'js/tinyshop.js',
    ];

    public $depends = [
    ];
}