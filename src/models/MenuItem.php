<?php

namespace skylineos\yii\menu\models;

use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use himiklab\sortablegrid\SortableGridBehavior;
use yii\db\Expression;

/**
 * The model class for the table MenuItem
 *
 * @property int $id
 * @property int $menuId
 * @property int|null $parentItemId
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
 */
class MenuItem extends \yii\db\ActiveRecord
{
    /**
     * The maximum sortOrder of all items in the table. Used for giving
     * new items a sortOrder on creation.
     *
     * @var int|null
     */
    private ?int $maxSort;

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
            'sort' => [
                'class' => SortableGridBehavior::className(),
                'sortableAttribute' => 'sortOrder'
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
            [['title', 'linkTo', 'linkTarget'], 'string', 'max' => 255],
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
}
