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
     * @var string the path to the base views directory
     */
    public string $viewPath = '@vendor/skylineos/yii-menu/src/views';

    /**
     * Allows for overloading or entirely different navClass
     * @see https://www.yiiframework.com/extension/yiisoft/yii2-bootstrap4/doc/api/2.0/yii-bootstrap4-nav
     *
     * @var string
     */
    public string $navClass = '\yii\bootstrap4\Nav';

    /**
     * Allows for overloading or entirely different dropdownClass
     * @see https://www.yiiframework.com/extension/yiisoft/yii2-bootstrap4/doc/api/2.0/yii-bootstrap4-dropdown
     *
     * @var string
     */
    public string $dropdownClass = '\yii\bootstrap4\Dropdown';

    public function init()
    {
        parent::init();
    }
}
