<?php

namespace skylineos\yii\menu;

class Menu extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'skylineos\yii\menu\controllers';

    /**
     * @todo confirm this path
     * @var string the path to the default menu view if no other template is provided
     */
    public string $viewPath = '@vendor/skylineos/yii-menu/views/menu/menu';

    public function init()
    {
        parent::init();
    }
}
