<?php

namespace skylineos\yii\menu\controllers;

use Yii;
use skylineos\yii\menu\models\Menu;
use skylineos\yii\menu\models\MenuItem;
use skylineos\yii\menu\models\search\MenuSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * MenuController implements the CRUD actions for Menu model.
 */
class MenuController extends \yii\web\Controller
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
                        'actions' => ['index', 'create', 'update', 'delete', 'sort'],
                        'roles' => \Yii::$app->controller->module->roles,
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'sort' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Menu models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MenuSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Menu model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Menu();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->session->setFlash('success', 'Menu Created, you may now populate it.');
            return $this->redirect(['update', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Menu model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        $menuItemSearchModel = new \skyline\yii\cms\models\search\MenuItemSearch();

        return $this->render('update', [
            'model' => $model,
            'parent' => \Yii::$app->request->get('parentItemId')
                ? MenuItem::findOne(\Yii::$app->request->get('parentItemId'))
                : null,
            'menuItemSearchModel' => $menuItemSearchModel,
        ]);
    }

    /**
     * Deletes an existing Menu model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Post endpoint used to sort menu-items for a given menu on drag & drop sort mechanism
     * Response is json encoded
     *
     * @return array
     */
    public function actionSort(): array
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $post = \Yii::$app->request->post();

        if (!$post['menuId']) {
            return [
                'statusCode' => 400,
                'message' => 'Missing required parameter: (int)menuId',
            ];
        }

        if (!$post['items']) {
            return [
                'statusCode' => 400,
                'message' => 'Missing required parameter: (string)items',
            ];
        }

        foreach ($post['items'] as $sortOrder => $item) {
            $menuItem = MenuItem::findOne($item['id']);
            $menuItem->parentItemId = strlen($item['parent_id']) > 0 ? $item['parent_id'] : null;
            $menuItem->sortOrder = $sortOrder;

            if ($menuItem->save() === false) {
                return [
                    'statusCode' => 500,
                    'message' => "Could not update sort order of items: $menuItem->getErrors()",
                ];
            }
        }

        return [
            'statusCode' => 200,
            'message' => 'OK',
        ];
    }

    /**
     * Finds the Menu model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Menu the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Menu::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
