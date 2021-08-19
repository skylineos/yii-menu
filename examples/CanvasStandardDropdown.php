<?php

namespace skylineos\examples;

use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\ArrayHelper;
use yii\bootstrap4\BootstrapPluginAsset;

class CanvasStandardDropdown extends \yii\bootstrap4\Dropdown
{
    /**
     * HTML options to apply to all submenu links
     */
    private const SUBMENU_LINK_WITH_DROPDOWN_OPTIONS = [
        'class' => 'menu-link dropdown-item',
        'data-toggle' => 'dropdown',
        'aria-haspopup' => true,
        'aria-expanded' => 'false',
        'role' => 'button',
    ];

    /**
     * HTML options to apply to all submenu links
     */
    private const SUBMENU_LINK_SANS_DROPDOWN_OPTIONS = [
        'class' => 'menu-link',
    ];

    /**
     * HTML options to apply to all first level LIs
     */
    private const FIRST_LEVEL_LI_OPTIONS = [
        'class' => 'dropdown mega-menu mega-menu-small menu-item sub-menu',
        'aria-expanded' => 'false,'
    ];

    /**
     * Submenu caret for all items that have sub items
     */
    private const SUBMENU_CARET = '<i class="icon-angle-down"></i>';

    /**
     * HTML options for all sub menu ul's
     */
    private const SUB_ITEM_UL_OPTIONS = [
        'class' => 'sub-menu-container',
    ];

    /**
     * HTML options for all sub menu li's
     */
    private const SUB_ITEM_LI_OPTIONS = [
        'class' => 'menu-item',
    ];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        Html::addCssClass($this->options, ['widget' => 'mega-menu-content mega-menu-style-2']);
    }

    /**
     * Renders the widget.
     * @throws InvalidConfigException
     */
    public function run()
    {
        BootstrapPluginAsset::register($this->getView());
        $this->registerClientEvents();
        return $this->renderItems($this->items, $this->options);
    }


    /**
     * Renders menu items.
     * @param array $items the menu items to be rendered
     * @param array $options the container HTML attributes
     * @return string the rendering result.
     * @throws InvalidConfigException if the label option is not specified in one of the items.
     * @throws \Exception
     */
    protected function renderItems($items, $options = [])
    {
        $lines = [];

        foreach ($items as $item) {
            $subItems = null;

            if (\array_key_exists('items', $item) && count($item['items']) > 0) {
                $label = Html::tag('div', \strip_tags($item['label']) . self::SUBMENU_CARET, []);
                $subItems = $this->renderItems($item['items']);
                $itemLink = Html::a($label, $item['url'], self::SUBMENU_LINK_WITH_DROPDOWN_OPTIONS);
            } else {
                $itemLink = Html::a($item['label'], $item['url'], self::SUBMENU_LINK_SANS_DROPDOWN_OPTIONS);
            }

            $lines[] = Html::tag('li', $itemLink . $subItems, self::FIRST_LEVEL_LI_OPTIONS);
        }


        return Html::tag('ul', implode("\n", $lines), self::SUB_ITEM_UL_OPTIONS);
    }
}
