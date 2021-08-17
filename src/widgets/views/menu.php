<?php

echo yii\bootstrap4\Nav::widget([
    'items' => $items,
    'options' => ['class' =>'nav-pills'], // set this to nav-tabs to get tab-styled navigation
]);
