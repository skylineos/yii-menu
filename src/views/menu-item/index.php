<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
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
  
  .sortable {
      padding-left: 0;
  }

  .sortable ul {
      list-style: none;
  }

  li, ul {
      padding-left: 1em;
  }
  
  .list-group-item .glyphicon {
    margin-right: 5px;
  }

  .menu-item-button {
      margin: 0 5px;
  }
');

$this->registerJsFile('https://code.jquery.com/ui/1.12.1/jquery-ui.min.js', [
    'depends' => [\yii\web\JqueryAsset::class]
]);

$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/nestedSortable/2.0.0/jquery.mjs.nestedSortable.min.js', [
    'depends' => [\yii\web\JqueryAsset::class]
]);

$this->registerJsFile('//cdn.jsdelivr.net/npm/sweetalert2@9', [
    'depends' => [\yii\web\YiiAsset::class]
]);

$this->registerJs("$(function() {
    const DEBUG = true;

    $('.list-group-item').on('click', function() {
      $('.fas', this)
        .toggleClass('fa-angle-right')
        .toggleClass('fa-angle-down');
    });
    
    $('.sortable').nestedSortable({
        listType: 'ul',
        handle: 'a',
        items: 'li',
        toleranceElement: '> a',
        excludeRoot: true,
        relocate: () => {
            var hierarchy = $('.sortable').nestedSortable('toArray', {startDepthCount: 0});
            $.post('sort', { menuId: $menuId, items: hierarchy }, function (data) {
                if (DEBUG === true) {
                    console.table(data);
                }
            });
        }
    });

    $('.add-item').click( function(e) {
        e.preventDefault();
        window.location.href = '/menu/menu-item/create?menuId=$menuId&parentItemId=' + $(this).attr('data-id');
    });

    $('.list-group-item').click( function(e) {
        $.get('/menu/menu-item/view?id=' + $(this).attr('data-id'), function(data) {
            $('#menuitem-id').val(data.id);
            $('#menuitem-title').val(data.title).removeAttr('disabled');
            $('#menuitem-linkto').val(data.linkTo).removeAttr('disabled').trigger('change');
            $('#menuitem-linktarget').val(data.linkTarget).removeAttr('disabled').trigger('change');
        });
    });

    $('.delete-item').click( function(e) {
        var itemId = $(this).attr('data-id');

        Swal.fire({
            title: 'Delete menu item?',
            text: 'This (and all sub-menu items) will be removed. This cannot be undone.',
            showCancelButton: true,
            confirmButtonText: `Delete`,
            type: 'warning'
          }).then((result) => {
            if (result.value == true) {
                $.post('/menu/menu-item/delete?id=' + itemId, function (data) {
                    // @todo: maybe some error handling?
                    var target = '#menuItem_' + itemId;
                    Swal.fire('Deleted!', 'Menu item (and all sub-menu items) removed!', 'success');
                    $(target).hide('slow', function() { 
                        $(target).remove(); 
                    });
                });
            } else {
              Swal.fire('Delete cancelled', '', 'info');
            }
          });
    });
  });", \yii\web\View::POS_READY);

function renderAdminTree(int $menuId, ?int $parentItemId = null, ?array $items = null)
{
    if ($items === null) {
        $items = getitems($menuId, $parentItemId);
    }

    if (\count($items) > 0) {
        $baseClass = 'list-group list-unstyled';
        $class = $parentItemId === null ? "$baseClass sortable" : "$baseClass collapse show";
        $id = $parentItemId === null ? 'menuItemWrapper' : "item-$parentItemId";
        echo Html::beginTag('ul', ['class' => $class, 'id' => $id]);
    }

    $x = 0;
    foreach ($items as $item) {
        echo Html::beginTag('li', [
            'id' => "menuItem_$item->id",
        ]);
        echo renderMenuItem($item, $menuId);

        if ($x === count($items)) {
            echo '</li>';
        }

        $subItems = getItems($menuId, $item->id);
        renderAdminTree($menuId, $item->id, $subItems);
    }

    if (\count($items) > 0) {
        echo Html::endTag('ul');
    }
}

function getItems(int $menuId, ?int $parentItemId = null): array
{
    return MenuItem::find()
        ->where(['menuId' => $menuId, 'parentItemId' => $parentItemId])
        ->orderBy('sortOrder ASC')
        ->all();
}

function renderMenuItem(skylineos\yii\menu\models\MenuItem $item, int $menuId): string
{
    $addItem = Html::tag(
        'span',
        '<i class="fal fa-plus-square"></i>',
        [
            'class' => 'menu-item-button add-item pull-right text-success',
            'data-id' => $item->id,
            'data-toggle' => 'tooltip',
            'title' => 'Create Sub-Item',
        ]
    );

    $deleteItem = Html::tag(
        'span',
        '<i class="fal fa-minus-square"></i>',
        [
            'class' => 'menu-item-button delete-item pull-right text-danger',
            'data-id' => $item->id,
            'data-toggle' => 'tooltip',
            'title' => 'Delete',
        ]
    );

    return Html::a(
        "<i class=\"fas fa-angle-down mr-2\"></i> $item->title $deleteItem $addItem",
        '#item-' . $item->id,
        [
            'class' => 'list-group-item',
            'data-toggle' => 'collapse',
            'data-id' => $item->id,
        ]
    );
}

?>

<div class="row">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body just-padding">
                <?= renderAdminTree($menuId) ?>
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
                    </div>
                    <div class="col">
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

                <div class="form-group">
                    <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                </div>

                <?php ActiveForm::end(); ?>


            </div>
        </div>
    </div>
</div>