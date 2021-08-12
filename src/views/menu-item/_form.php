<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model skylineos\yii\menu\models\MenuItem */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="menu-item-form">

    <?php $form = ActiveForm::begin([
        'action' => "create?menuId=$model->menuId"
    ]); ?>
    
    <?= $form->field($model, 'menuId')->hiddenInput(['value' => $model->menuId])->label(false) ?>
    <?= $form->field($model, 'parentItemId')->hiddenInput(['value' => $model->parentItemId])->label(false) ?>

    <div class="row">
        <div class="col">
            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col">
            <?= $form->field($model, 'linkTo')->dropDownList(skylineos\yii\menu\models\MenuItem::getTargets()) ?>
        </div>
        <div class="col">
            <?= $form->field($model, 'linkTarget')->dropDownList(['_self' => 'Same Window', '_blank' => 'New Window']) ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
