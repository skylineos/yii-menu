<?php

namespace skylineos\yii\menu\widgets;

use skylineos\yii\menu\models\MenuItem;

class SkyMenuWidget extends \yii\base\Widget
{
    /**
     * Pass the path to your template view. Should be some incarnation of \yii\bootstrap\Menu
     * If the simplicity of your menu suits this widget, you can always just override the view
     *
     * @var string
     */
    public string $view = 'menu';

    /**
     * The id of the menu for which we should build the widget
     *
     * @var integer
     */
    public int $menuId;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        if ($this->menuId === null) {
            $this->menuId = Menu::find()->one();
        }
    }

    /**
     * @inheritDoc
     */
    public function run()
    {
        return $this->render($this->view, [
            'items' => $this->getItems(),
        ]);
    }

    /**
     * Recursively build the menu items array
     *
     * @param integer $parentItemId
     * @return array
     */
    private function getItems(int $parentItemId = null): array
    {
        $items = [];
        $menuItems = MenuItem::find()
            ->where([
                'menuId' => $this->menuId,
                'parentItemId' => $parentItemId,
            ])
            ->orderBy('sortOrder ASC')
            ->all();

        foreach ($menuItems as $item) {
            $items[] = [
                'label' => $item->title,
                'url' => $item->linkTo,
                'items' => $this->getItems($item->id),
                'linkOptions' => [
                    'target' => $item->linkTarget,
                ]
            ];
        }

        return $items;
    }
}
