<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use skylineos\yii\menu\models\MenuItem;
use skylineos\yii\menu\assets\ExtAsset;

/**
 * Define MENU_ID for use in our main.js
 */
$this->registerJs("const MENU_ID = $menuId;", \yii\web\View::POS_HEAD);
ExtAsset::register($this);

?>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body just-padding">
                <h4>Menu Items <small>(Drag and drop to sort)</small></h4>
                <hr>

                <?= $menuTree ?>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <?= Html::a(
    '<i class="fal fa-plus-square fa-2x"></i>',
    ["menu-item/create?menuId=$menuId"],
    ['data-toggle' => 'tooltip', 'title' => 'New Root Item', 'class' => 'mr-2']
) ?>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card">
            <div class="card-body">

                <?php $form = ActiveForm::begin([
                    'action' => '/menu/menu-item/update',
                ]); ?>

                <?= Html::input('hidden', 'MenuItem[id]', null, [
                    'id' => 'menuitem-id',
                ]) ?>

                <h4>Menu Item Details</h4>
                <hr>

                <div class="row">

                    <div class="col">
                        <div class="form-group">
                            <label>Title</label>
                            <?= Html::input('text', 'MenuItem[title]', null, [
                                'class' => 'form-control',
                                'id' => 'menuitem-title',
                                'disabled' => true,
                            ]) ?>
                        </div>

                        <div class="form-group">
                            <label>Menu Template</label>
                            <?= Html::dropDownList(
                                'MenuItem[template]',
                                null,
                                \Yii::$app->getModule('menu')->templates,
                                [
                                    'id' => 'menuitem-template',
                                    'disabled' => true,
                                    'style' => 'display: inline-block; width: 100%;'
                                ]
                            ) ?>
                            <div class="help-text">Will be disabled if the menu item depth exceeds that which templating can support</div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label>Link To</label>
                            <?= Html::dropDownList(
                                'MenuItem[linkTo]',
                                null,
                                MenuItem::getTargets(),
                                [
                                    'class' => 'form-control',
                                    'id' => 'menuitem-linkto',
                                    'disabled' => true,
                                ]
                            ) ?>
                        </div>

                        <div class="form-group">
                            <label>Link Target</label>
                            <?= Html::dropDownList(
                                'MenuItem[linkTarget]',
                                null,
                                [
                                    '_self' => 'Same Window',
                                    '_blank' => 'New Window'
                                ],
                                [
                                    'class' => 'form-control',
                                    'id' => 'menuitem-linktarget',
                                    'disabled' => true,
                                ]
                            ) ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                        </div>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>