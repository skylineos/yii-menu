<?php

namespace skylineos\yii\menu;

class Module extends \yii\base\Module
{
    public const DEFAULT_PK = 'id';

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'skylineos\yii\menu\controllers';

    /**
     * @inheritDoc
     */
    public $defaultRoute = 'menu';

    /**
     * The array of slug => title targets.
     * @see readme
     */
    public array $targets = [];

    /**
     * List of namespace => name templates to offer as styles when managing menus
     *
     * @var array
     */
    public array $templates = [
        'skylineos\yii\menu\widgets\MenuWidget' => 'Default',
    ];

    /**
     * List of roles that are able to access this module
     *
     * @var array
     */
    public array $roles = [
        '@',
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
