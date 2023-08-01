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
        'https://at.alicdn.com/t/font_1681579_ymtux4xwd9.css',
        'css/tinyshop.css',
        'css/font-demo.css',
    ];

    public $js = [
        'js/tinyshop.js',
        'js/vuedraggable.min.js',
    ];

    public $depends = [
    ];
}
