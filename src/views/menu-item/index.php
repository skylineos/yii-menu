<?php

use yii\helpers\Html;
use skylineos\yii\menu\models\MenuItem;

$this->registerCss('
.just-padding {
    padding: 15px;
}

.list-group.list-group-root {
    padding: 0;
    overflow: hidden;
}

.list-group.list-group-root .list-group {
    margin-bottom: 0;
}

.list-group.list-group-root .list-group-item {
    border-radius: 0;
    border-width: 1px 0 0 0;
}

.list-group.list-group-root > .list-group-item:first-child {
    border-top-width: 0;
}

.list-group.list-group-root > .list-group > .list-group-item {
    padding-left: 30px;
}

.list-group.list-group-root > .list-group > .list-group > .list-group-item {
    padding-left: 45px;
}

.list-group-item .fa {
    margin-right: 5px;
}

.collapse {
  display: none;
  &.show {
    display: block;
  }
}
');

$this->registerJs("$(function() {
        
    $('.list-group-item').on('click', function() {
      $('.fa', this)
        .toggleClass('fa-caret-right')
        .toggleClass('fa-caret-down');
    });
  
  });");

function renderAdminTree(int $menuId, ?int $parentItemId = null, ?array $items = null)
{
    if ($items === null) {
        $items = getitems($menuId, $parentItemId);
    }

    if (\count($items) > 0) {
        $class = 'list-group';
        $class .= $parentItemId === null ? 'list-group-root' : 'collapse';
        echo '<div class="' . $class . '" role="tablist">';
    }

    foreach ($items as $item) {
        echo renderMenuItem($item, $menuId);

        $subItems = getItems($menuId, $item->id);
        renderAdminTree($menuId, $item->id, $subItems);
    }

    if (\count($items) > 0) {
        echo '</div>';
    }
}

function getItems(int $menuId, ?int $parentItemId = null): array
{
    return MenuItem::find()->where(['menuId' => $menuId, 'parentItemId' => $parentItemId])->all();
}

function renderMenuItem(skylineos\yii\menu\models\MenuItem $item, int $menuId): string
{
    return Html::tag('li', $item->title . $addButton, ['class' => 'list-group-item']);
}

?>

<div class="row">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <?= renderAdminTree($menuId) ?>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <?= Html::a(
    '<i class="fas fa-tree fa-2x"></i>',
    ["menu-item/create?menuId=$menuId"],
    ['data-toggle' => 'tooltip', 'title' => 'New Root Item']
) ?>

                <?= Html::a(
    '<i class="far fa-plus-square fa-2x"></i>',
    ["menu-item/create?menuId=$menuId"],
    ['data-toggle' => 'tooltip', 'title' => 'New Root Item']
) ?>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <div class="form-group field-menuitem-title required has-error">
                            <label class="control-label" for="menuitem-title">Title</label>
                            <input disabled type="text" id="menuitem-title" class="form-control" name="MenuItem[title]"
                                maxlength="255" aria-required="true" aria-invalid="true">

                            <div class="help-block">Title cannot be blank.</div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group field-menuitem-linkto">
                            <label class="control-label" for="menuitem-linkto">Link</label>
                            <select disabled id="menuitem-linkto" class="form-control" name="MenuItem[linkTo]"
                                data-select2-id="menuitem-linkto" tabindex="-1" aria-hidden="true">
                                <optgroup label="All Routes">
                                    <option value="my-page" data-select2-id="2">My Page</option>
                                </optgroup>
                            </select>
                            <div class="help-block"></div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group field-menuitem-linktarget">
                            <label class="control-label" for="menuitem-linktarget">Link Target</label>
                            <select disabled id="menuitem-linktarget" class="form-control" name="MenuItem[linkTarget]"
                                data-select2-id="menuitem-linktarget" tabindex="-1" aria-hidden="true">
                                <option value="_self" data-select2-id="5">Same Window</option>
                                <option value="_blank">New Window</option>
                            </select>

                            <div class="help-block"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>