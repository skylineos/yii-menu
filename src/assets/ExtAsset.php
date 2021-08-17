<?php

namespace skylineos\yii\menu\assets;

use yii\web\AssetBundle;

class ExtAsset extends AssetBundle
{
    public $sourcePath = '@vendor/skylineos/yii-menu/src/assets';

    public $css = [
        'css/main.css',
    ];

    public $js = [
        'https://code.jquery.com/ui/1.12.1/jquery-ui.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/nestedSortable/2.0.0/jquery.mjs.nestedSortable.min.js',
        '//cdn.jsdelivr.net/npm/sweetalert2@9',
        'js/main.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
        'yii\bootstrap4\BootstrapAsset'
    ];
}
