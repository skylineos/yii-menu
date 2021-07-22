<?php

use yii\helpers\Html;
use yii\grid\GridView;
use skylineos\yii\menu\models\Menu;

/* @var $this yii\web\View */
/* @var $searchModel skylineos\yii\menu\models\search\MenuSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Menus';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="menu-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Menu', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
            ],
            //'id',
            'title',
            'template',
            [
                'attribute' => 'status',
                'filter' => Menu::STATUS_TITLE,
                'value' => function ($model) {
                    return Menu::STATUS_TITLE[$model->status];
                }
            ],
            'createdBy',
            'modifiedBy',
            [
                'attribute' => 'dateCreated',
                'format' => 'datetime',
                'filter' => false,
            ],
            [
                'attribute' => 'lastModified',
                'format' => 'datetime',
                'filter' => false,
            ],
        ],
    ]); ?>


</div>
