# Yii2 Menu

### Configuration

> config/web.php

```php
'modules' => [
    'menu' => [
        'class' => 'skylineos\yii\menu\Module',
    ],
],
```

### Migrations

`php yii migrate/up --migrationPath=@vendor/skyline/yii-menu/migrations`

It is recommended though in no way required you create a migration in your own app to link the 
properties `createdBy` and `modifiedBy` as foreign keys to the primary keys of your User model 
(whatever that may be).