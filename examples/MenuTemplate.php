<?php

namespace app\modules\frontend\widgets\navigation;

class MyMenuTemplate
{
    /**
     * The template used to render the template. {$label} will be replaced with the
     * actual label. If you put HTML in the template, be sure to set encodeLabels to false
     *
     * @var string
     */
    public string $labelTemplate = '<div>{$label}</div>';

    /**
     * @see https://www.yiiframework.com/extension/yiisoft/yii2-bootstrap4/doc/api/2.0/yii-bootstrap4-nav#$encodeLabels-detail
     *
     * @var boolean
     */
    public bool $encodeLabels = false;

    /**
     * @see https://www.yiiframework.com/extension/yiisoft/yii2-bootstrap4/doc/api/2.0/yii-bootstrap4-widget#$options-detail
     *
     * @var array
     */
    public array $navOptions = ['class' => 'menu-container'];

    /**
     * @see https://www.yiiframework.com/doc/api/2.0/yii-helpers-baseurl#to()-detail
     * @property scheme
     *
     * @var boolean
     */
    public bool $urlScheme = true;

    /**
     * @https://www.yiiframework.com/extension/yiisoft/yii2-bootstrap4/doc/api/2.0/yii-bootstrap4-nav#$items-detail
     * @property options
     *
     * @var array
     */
    public array $itemOptions = ['class' => 'menu-item'];

    /**
     * @https://www.yiiframework.com/extension/yiisoft/yii2-bootstrap4/doc/api/2.0/yii-bootstrap4-nav#$items-detail
     * @property linkOptions
     *
     * @var array
     */
    public array $linkOptions = ['class' => 'menu-link'];
}
