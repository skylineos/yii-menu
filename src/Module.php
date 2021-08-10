<?php

namespace skylineos\yii\menu;

class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'skylineos\yii\menu\controllers';

    /**
     * @inheritDoc
     */
    public $defaultRoute = 'menu';

    /**
     * List of namespace => name templates to offer as styles when managing menus
     *
     * @var array
     */
    public $templates = [
        'skylineos\yii\menu\widgets\MenuWidget' => 'Default',
    ];

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
