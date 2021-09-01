<?php

namespace skylineos\yii\menu\assets;

use yii\web\AssetBundle;

class ExtAsset extends AssetBundle
{
    public $sourcePath = '@vendor/skylineos/yii-menu/src/assets';

    public $css = [
        'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
        'css/main.css',
    ];

    public $js = [
        'https://code.jquery.com/ui/1.12.1/jquery-ui.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/nestedSortable/2.0.0/jquery.mjs.nestedSortable.min.js',
        '//cdn.jsdelivr.net/npm/sweetalert2@9',
        'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
        'js/main.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
        'yii\bootstrap4\BootstrapAsset'
    ];
}
