<?php

namespace skylineos\yii\menu\widgets;

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

    /**
     * The template used to render the template. {$label} will be replaced with the
     * actual label. If you put HTML in the template, be sure to set encodeLabels to false
     *
     * @var string
     */
    public string $labelTemplate = '{$label}';

    /**
     * @see https://www.yiiframework.com/doc/api/2.0/yii-helpers-baseurl#to()-detail
     * @property scheme
     *
     * @var boolean
     */
    public bool $urlScheme = true;

    /**
     * @https://www.yiiframework.com/extension/yiisoft/yii2-bootstrap4/doc/api/2.0/yii-bootstrap4-nav#$items-detail
     * @property options
     *
     * @var array
     */
    public array $itemOptions = ['class' => 'menu-item'];

    /**
     * @https://www.yiiframework.com/extension/yiisoft/yii2-bootstrap4/doc/api/2.0/yii-bootstrap4-nav#$items-detail
     * @property linkOptions
     *
     * @var array
     */
    public array $linkOptions = ['class' => 'menu-link'];


    private $template;

    public function getViewPath()
    {
        return \Yii::getAlias('@vendor/skylineos/yii-menu/src/widgets/views', $throwException = true);
    }

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
        $menuModule = \Yii::$app->getModule('menu');
        $this->template = new $menuTemplate();

        return $menuModule->navClass::widget([
            'items' => $this->getItems(null),
            'encodeLabels' => $this->template->encodeLabels ?? true,
            'options' => $this->template->wrapperOptions ?? [],
            'dropdownClass' => $menuModule->dropdownClass,
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

            $thisItem = [
                'label' => \str_replace('{$label}', $item->title, $this->template->labelTemplate ?? '{$label}'),
                'url' => Url::to($item->linkTo, $this->template->urlScheme ?? true),
                'options' => $this->template->itemOptions ?? [],
                'linkOptions' => \array_merge($this->template->linkOptions ?? [], ['target' => $item->linkTarget]),
                'items' => $this->getItems($item->id),
            ];

            if (isset($this->template->dropdownClass)) {
                $thisItem['dropdownClass'] = $this->template->dropdownClass;
            }

            $items[] = $thisItem;
        }

        return $items;
    }
}
