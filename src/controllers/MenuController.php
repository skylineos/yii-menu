<?php

namespace skylineos\yii\menu\controllers;

use Yii;
use skylineos\yii\menu\models\Menu;
use skylineos\yii\menu\models\MenuItem;
use skylineos\yii\menu\models\search\MenuSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Html;

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
                        'actions' => ['index', 'create', 'update', 'delete', 'sort', 'demo'],
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
     * {@inheritDoc}
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        $this->viewPath = rtrim(\Yii::$app->controller->module->viewPath, '/') . '/menu';
        return true;
    }

    public function actionDemo(int $id): string
    {
        return $this->render('demo', [
            'menuId' => $id,
        ]);
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

        $menuItemSearchModel = new \skylineos\yii\menu\models\search\MenuItemSearch();

        return $this->render('update', [
            'model' => $model,
            'menuTree' => $this->renderAdminTree($model->id),
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

    private function renderAdminTree(int $menuId, ?int $parentItemId = null, ?array $items = null)
    {
        $ret = '';

        if ($items === null) {
            $items = $this->getItems($menuId, $parentItemId);
        }

        if (\count($items) > 0) {
            $baseClass = 'list-group list-unstyled';
            $class = $parentItemId === null ? "$baseClass sortable" : "$baseClass collapse show";
            $id = $parentItemId === null ? 'menuItemWrapper' : "item-$parentItemId";
            $ret .= Html::beginTag('ul', ['class' => $class, 'id' => $id]);
        }

        $x = 0;
        foreach ($items as $item) {
            $ret .= Html::beginTag('li', [
                'id' => "menuItem_$item->id",
            ]);
            $ret .= $this->renderMenuItem($item, $menuId);

            if ($x === count($items)) {
                $ret .= '</li>';
            }

            $subItems = $this->getItems($menuId, $item->id);
            $ret .= $this->renderAdminTree($menuId, $item->id, $subItems);
        }

        if (\count($items) > 0) {
            $ret .= Html::endTag('ul');
        }

        return $ret;
    }

    private function getItems(int $menuId, ?int $parentItemId = null): array
    {
        return MenuItem::find()
            ->where(['menuId' => $menuId, 'parentItemId' => $parentItemId])
            ->orderBy('sortOrder ASC')
            ->all();
    }

    private function renderMenuItem(\skylineos\yii\menu\models\MenuItem $item, int $menuId): string
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
}
