# Yii2 Menu

## Configuration

> config/web.php

```php
'modules' => [
    'menu' => [
        'class' => 'skylineos\yii\menu\Module',
        'viewPath' => '@app/path/to/my/views',

        // Additional templates can be added as such: (namespace => display/friendly name)
        'templates' => [
            'skylineos\yii\menu\widgets\MenuWidget' => 'Default',
            'app\widgets\menus\MegaMenuWidget' => 'Mega Menu',
        ],
        'targets' => [
            // See 'Defining Targets'
        ]
    ],
],
```

## Defining Targets

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
            'pk' => 'id' // The primary key on the foreign model. If not provided, 'id' will be assumed
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
            'pk' => 'id' // The primary key on the foreign model. If not provided, 'id' will be assumed
            'fk' => 'modelId',
            'property' => 'metaTitle',
        ],
        'where' => [],
        'orderBy' => 'Product.sortOrder ASC',
        'dropdownGroup' => 'Products',
    ],
],
```


## Migrations

`php yii migrate/up --migrationPath=@vendor/skyline/yii-menu/migrations`

It is recommended though in no way required you create a migration in your own app to link the 
properties `createdBy` and `modifiedBy` as foreign keys to the primary keys of your User model 
(whatever that may be).