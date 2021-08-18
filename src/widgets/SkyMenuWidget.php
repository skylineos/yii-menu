<?php

namespace app\modules\frontend\widgets\navigation;

use yii\base\Widget;
use yii\helpers\Url;
use skylineos\yii\menu\models\Menu;
use skylineos\yii\menu\models\MenuItem;

class SkyMenuWidget extends Widget
{
    /**
     * The id of the menu to load
     *
     * @var integer
     */
    public ?int $menuId = null;

    private $template;

    public function run()
    {
        if ($this->menuId === null) {
            throw new yii\web\BadRequestHttpException('You must provide a menuId');
        }

        $menu = Menu::findOne($this->menuId);

        if (!$menu) {
            throw new yii\web\NotFoundHttpException('Menu was not found');
        }

        return $this->render('sky-menu', [
            'menu' => $this->buildMenu($menu->id, $menu->template),
        ]);
    }

    private function buildMenu(int $menuId, string $menuTemplate): string
    {
        $this->template = new $menuTemplate();

        return \yii\bootstrap4\Nav::widget([
            'items' => $this->getItems(null),
            'encodeLabels' => $this->template->encodeLabels,
            'options' => $this->template->wrapperOptions,
        ]);
    }

    private function getItems(?int $parentItemId): array
    {
        $items = [];

        $menuItems = MenuItem::find()
            ->where([
                'parentItemId' => $parentItemId,
                'menuId' => $this->menuId,
            ])
            ->orderBy('sortOrder ASC')
            ->all();

        foreach ($menuItems as $item) {
            if ($item->template !== null) {
                $itemTemplate = $item->template;
                $this->template = new $itemTemplate();
            }

            $items[] = [
                'label' => \str_replace('{$label}', $item->title, $this->template->labelTemplate),
                'url' => Url::to($item->linkTo, $this->template->urlScheme),
                'options' => $this->template->itemOptions,
                'linkOptions' => \array_merge($this->template->linkOptions, ['target' => $item->linkTarget]),
                'items' => $this->getItems($item->id),
            ];
        }

        return $items;
    }
}
