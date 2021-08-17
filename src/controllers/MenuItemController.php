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
                        'actions' => ['create', 'update', 'delete', 'view'],
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
     * {@inheritDoc}
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        $this->viewPath = rtrim(\Yii::$app->controller->module->viewPath, '/') . '/menu-item';
        return true;
    }

    public function actionView(int $id): array
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = $this->findModel($id);

        return [
            'id' => $model->id,
            'title' => $model->title,
            'linkTo' => $model->linkTo,
            'linkTarget' => $model->linkTarget,
            'template' => $model->template,
            'templateDisabled' => MenuItem::exceedsTemplateThreshold($model->parentItemId),
        ];
    }

    /**
     * Creates a new Menu Item model.
     * If creation is successful, the browser will be redirected to the Menu Update page
     * @return mixed
     */
    public function actionCreate(int $menuId, ?int $parentItemId = null)
    {
        $model = new MenuItem();
        $model->menuId = $menuId;
        $model->parentItemId = $parentItemId;
        $templateDisabled = false;

        if (MenuItem::exceedsTemplateThreshold($parentItemId) === true) {
            $templateDisabled = true;
            $model->template = null; // a null template equates to inheriting the parent
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->session->setFlash('success', 'Menu Item Created.');
            return $this->redirect(['menu/update', 'id' => $model->menuId]);
        }

        return $this->render('create', [
            'model' => $model,
            'templateDisabled' => $templateDisabled,
        ]);
    }

    /**
     * Updates an existing MenuItem model.
     * If update is successful, the browser will be redirected to the menu update page
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate()
    {
        $model = $this->findModel(Yii::$app->request->post()['MenuItem']['id']);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['menu/update', 'id' => $model->menuId]);
        }
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
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $this->findModel($id)->delete();
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
        if (($model = MenuItem::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
