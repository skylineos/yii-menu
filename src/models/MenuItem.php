<?php

namespace skylineos\yii\menu\models;

use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * The model class for the table MenuItem
 *
 * @property int $id
 * @property int $menuId
 * @property int|null $parentItemId
 * @property string $template
 * @property string $title
 * @property string|null $linkTo
 * @property string|null $linkTarget
 * @property int $sortOrder
 * @property string $dateCreated  defaultExpression('NOW()'),
 * @property string $lastModified  defaultExpression('NOW()'),
 * @property int $createdBy
 * @property int $modifiedBy
 *
 * @property Menu $menu
 * @property MenuItem $parentItem
 *
 * @property int $maxSort
 * @property array $targets
 */
class MenuItem extends \yii\db\ActiveRecord
{
    /**
     * Depth after which template option is disabled (to prevent nested template logic)
     */
    private const TEMPLATE_THRESHOLD = 0;

    /**
     * The maximum sortOrder of all items in the table. Used for giving
     * new items a sortOrder on creation.
     *
     * @var int|null
     */
    public ?int $maxSort;

    /**
     * @inheritDoc
     *
     * @return string
     */
    public static function tableName(): string
    {
        return 'MenuItem';
    }

    /**
     * @inheritDoc
     *
     * @return array
     */
    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => ['dateCreated', 'lastModified'],
                'updatedAtAttribute' => ['lastModified'],
                'value' => new Expression('NOW()'),
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'createdBy',
                'updatedByAttribute' => 'modifiedBy',
                'defaultValue' => 1,
                'preserveNonEmptyValues' => true,
            ],
        ];
    }

    /**
     * @inheritDoc
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            [['title', 'linkTo', 'linkTarget', 'template'], 'string', 'max' => 255],
            [['title', 'menuId'], 'required'],
            [['menuId', 'parentItemId', 'sortOrder', 'modifiedBy', 'createdBy'], 'integer'],
            [['sortOrder'], 'default', 'value' => function ($model, $attribute) {
                $maxSortOrder = $model::find()
                    ->select(['MAX(sortOrder) as maxSort'])
                    ->where([
                        'menuId' => $model->menuId,
                        'parentItemId' => $model->parentItemId,
                    ])
                    ->one();
                return $maxSortOrder->maxSort < 1 ? 1 : $maxSortOrder->maxSort + 1;
            }],
            [['maxSort'], 'safe'],
        ];
    }

    /**
     * @inheritDoc
     *
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'menuId' => 'Menu ID',
            'parentItemId' => 'Parent Item ID',
            'template' => 'Menu Template',
            'title' => 'Title',
            'linkTo' => 'Link',
            'linkTarget' => 'Link Target',
            'sortOrder' => 'Sort Order',
            'dateCreated' => 'Date Created',
            'lastModified' => 'Last Modified',
            'createdBy' => 'Created By',
            'modifiedBy' => 'Modified By',
        ];
    }

    /**
     * Gets query for [Menu]
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMenu(): yii\db\ActiveQuery
    {
        return $this->hasOne(Menu::className(), ['id' => 'menuId']);
    }

    /**
     * Gets query for [Menu]
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParentItem(): yii\db\ActiveQuery
    {
        return $this->hasOne(self::className(), ['id' => 'parentItemId']);
    }

    /**
     * Builds the list of slugs => titles for the drop down of a menu item
     *
     * @return array
     */
    public static function getTargets(): array
    {
        $module = \Yii::$app->getModule('menu');
        $targets = [];

        foreach ($module->targets as $targetClass => $targetConfig) {
            $query = $targetClass::find();
            $group = \array_key_exists('dropdownGroup', $targetConfig) ? $targetConfig['dropdownGroup'] : $targetClass;
            $pk = \array_key_exists('pk', $targetConfig) ? $targetConfig['pk'] : $module::DEFAULT_PK;

            /**
             * @todo: I imagine this can be cleaned up
             */
            if (!\is_array($targetConfig['display'])) {
                $query->andFilterWhere(\array_key_exists('where', $targetConfig) ? $targetConfig['where'] : []);

                if (\array_key_exists('orderBy', $targetConfig)) {
                    $query->orderBy($targetConfig['orderBy']);
                }

                $targets[$group] = ArrayHelper::map(
                    $query->all(),
                    $targetConfig['slug'],
                    $targetConfig['display']
                );
            } else {
                // literal
                if ($targetConfig['literal'] === true) {
                    $tableName = $targetConfig['className']::tableName;

                    $leftJoinery = $tableName . '.' . $pk;
                    $rightJoinery = $targetClass . '.' . $targetConfig['fk'];

                    $query->leftJoin($tableName, "$leftJoinery = $rightJoinery");

                    $query->andFilterWhere(\array_key_exists('where', $targetConfig) ? $targetConfig['where'] : []);

                    if (\array_key_exists('orderBy', $targetConfig)) {
                        $query->orderBy($targetConfig['orderBy']);
                    }

                    $targets[$group] = ArrayHelper::map(
                        $query->all(),
                        $targetConfig['slug'],
                        $targetConfig['display']['property']
                    );
                } else {
                    // Non literal
                    $query->andFilterWhere(\array_key_exists('where', $targetConfig) ? $targetConfig['where'] : []);

                    if (\array_key_exists('orderBy', $targetConfig)) {
                        $query->orderBy($targetConfig['orderBy']);
                    }

                    $results = $query->all();

                    $targets[$group] = ArrayHelper::map(
                        $query->all(),
                        $targetConfig['slug'],
                        function ($model) use ($pk, $targetConfig) {
                            $fk = $targetConfig['display']['fk'];
                            $className = $model->model;
                            $subQuery = $className::find();
                            $subQuery->where([$pk => $model->$fk]);
                            $subQuery->andFilterWhere(
                                \array_key_exists('where', $targetConfig['display'])
                                    ? $targetConfig['display']['where']
                                    : []
                            );

                            if (\array_key_exists('orderBy', $targetConfig['display'])) {
                                $subQuery->orderBy($targetConfig['display']['orderBy']);
                            }

                            return $subQuery->one()->{$targetConfig['display']['property']};
                        }
                    );
                }
            }
        }

        return $targets;
    }

    /**
     * Ensures that the depth of the menuItem being created or updated does not exceed
     * the TEMPLATE THRESHOLD - that being the maximum depth at which we'll still conisder
     * templates
     *
     * If you exhibit symtoms of Nested Template Syndrome, please contact your System Administrator
     *
     * @param integer|null $parentItemId
     * @return boolean
     */
    public static function exceedsTemplateThreshold(?int $parentItemId): bool
    {
        if ($parentItemId === null) {
            return false;
        }

        $x = 0;

        while ($x < self::TEMPLATE_THRESHOLD) {
            $parent = MenuItem::find()->where(['id' => $parentItemId])->one();
            $parentItemId = $parent->parentItemId;

            if ($parentItemId === null) {
                return false;
            }

            $x++;
        }

        return true;
    }
}
