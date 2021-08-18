# Yii2 Menu

## Installation

*Composer:*

`"skylineos/yii-menu": "~1.0"`

*Run Migrations:*

`php yii migrate/up --migrationPath=vendor/skylineos/yii-menu/src/migrations`

> It is recommended though in no way required you create a migration in your own app to link the 
> properties `createdBy` and `modifiedBy` as foreign keys to the primary keys of your User model 
> (whatever that may be).

Once configured (below), you should be able to access the manager at /menu/menu/index



## Configuration

> config/web.php

```php
'modules' => [
    'menu' => [
        'class' => 'skylineos\yii\menu\Module',
        'viewPath' => '@app/path/to/my/admin/views', // eg. @app/modules/cms/views. The system looks for [menu|menu-item] folders
        'roles' => ['@'], // optional yii authorization roles
        'dropdownClass' => 'app\path\to\dropDownClass', // @see https://www.yiiframework.com/extension/yiisoft/yii2-bootstrap4/doc/api/2.0/yii-bootstrap4-dropdown

        /**
         * Additional templates can be added as such: (namespace => display/friendly name)
         * @see [Building Templates]
         */
        'menuTemplates' => [
            'app\widgets\menus\MenuWidget' => 'My Custom Widget',
            'app\widgets\menus\OtherMenuWidget' => 'My Other Custom Widget',
        ],
        'dropdownTemplates' => [
            'app\widget\menus\DropDownWidget' => 'My Custom Drop Down',
            'app\widget\menus\MegaMenuWidget' => 'My Mega Menu Drop Down',
        ]
        'targets' => [
            // See 'Defining Targets'
        ]
    ],
],
```

## Defining Link Targets

Targets (defining what the menu items can link to) can be handled three ways, all within the 'targets' property of the 
module:

```
'targets' => [
    // Here, slug will be mapped to title, both pulled directly from Content
    'app\models\Content' => [
        'slug' => 'slug',
        'display' => 'title',
        'where' => [], // optional
        'orderBy' => 'title ASC', // optional
        'dropdownGroup' => 'Pages',
    ],

    // This will interpret the model property of Router and map the 'slug' to the 'model.title' based on the fk
    // literal => false
    'app\models\Router' => [
        'slug' => 'slug',
        'display' => [
            'literal' => false,
            'className' => 'model',
            'pk' => 'id', // The primary key on the foreign model. If not provided, 'id' will be assumed
            'fk' => 'modelId',
            'property' => 'title',
            'where' => [], // optional
            'orderBy' => 'title ASC' // optional
        ],
        'where' => [],
        'orderBy' => 'title ASC',
        'dropdownGroup' => 'All Routes',
    ],

    // This will load the literal map of ProductCatalog.slug to Product.metaTitle.
    // literal => true
    'app\models\ProductCatalog' => [
        'slug' => 'slug',
        'display' => [
            'literal' => true,
            'className' => 'app\models\Product',
            'pk' => 'id', // The primary key on the foreign model. If not provided, 'id' will be assumed
            'fk' => 'modelId',
            'property' => 'metaTitle',
        ],
        'where' => [],
        'orderBy' => 'Product.sortOrder ASC',
        'dropdownGroup' => 'Products',
    ],
],
```

## Usage

This package comes with a helper widget that will process your menu and render it using whatever templates 
you've configured on your menu items.

Make sure your templates (`menuTemplates` and `dropDownTemplates`) meet the requirements. See Building Templates

If these requirements do not suit your needs, you are, of course, more than welcome to develop your own widget that 
renders the items however you like.

`<?= \skylineos\yii\menu\widgets\SkyMenuWidget::widget(['menuId' => $menuId]) ?>`

Where `$menuId` is the `id` of the Menu you wish to render.

## Building Templates

Menu Templates:

See `examples/MenuTemplate.php` in this repository

DropDown Templates:

See `examples/DropDownTemplate.php` in this repository