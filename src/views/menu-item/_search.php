<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model skylineos\yii\menu\models\search\MenuItemSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="menu-item-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'menuId') ?>

    <?= $form->field($model, 'parentItemId') ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'linkTo') ?>

    <?php // echo $form->field($model, 'linkTarget') ?>

    <?php // echo $form->field($model, 'sortOrder') ?>

    <?php // echo $form->field($model, 'dateCreated') ?>

    <?php // echo $form->field($model, 'lastModified') ?>

    <?php // echo $form->field($model, 'createdBy') ?>

    <?php // echo $form->field($model, 'modifiedBy') ?>

    <?php // echo $form->field($model, 'menuData') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
