<?php

namespace addons\TinyShop\merchant\assets;

use yii\web\AssetBundle;

/**
 * 静态资源管理
 *
 * Class AppAsset
 * @package addons\TinyShop\merchant\assets
 * @author jianyan74 <751393839@qq.com>
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