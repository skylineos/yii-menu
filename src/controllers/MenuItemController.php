<?php

namespace skylineos\yii\menu\controllers;

use Yii;
use skylineos\yii\menu\models\MenuItem;
use skylineos\yii\menu\models\search\MenuItemSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * MenuController implements the CRUD actions for Menu model.
 */
class MenuItemController extends \yii\web\Controller
{
    /**
    * {@inheritdoc}
    */
    public function behaviors()
    {
        return [
           'access' => [
               'class' => AccessControl::className(),
               'rules' => [
                   [
                       'allow' => true,
                       'actions' => ['index', 'create', 'update', 'delete'],
                       'roles' => \Yii::$app->controller->module->roles,
                   ],
               ],
           ],
           'verbs' => [
               'class' => VerbFilter::className(),
               'actions' => [
                   'delete' => ['POST'],
               ],
           ],
        ];
    }

    /**
     * Creates a new Menu Item model.
     * If creation is successful, the browser will be redirected to the Menu Update page
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MenuItem();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->session->setFlash('success', 'Menu Item Created.');
            return $this->redirect(['menu/update', 'id' => $model->menuId]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }
}
