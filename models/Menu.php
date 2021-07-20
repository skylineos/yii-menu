<?php

namespace skylinos\yii\menu\models;

use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * The model class for the table Menu
 *
 * @property int $id
 * @property string $title
 * @property int $published  defaultValue(1)
 * @property string $template  defaultValue('@vendor/skyline/yii-menu/views/menu/menu.php')
 * @property string $dateCreated  defaultExpression('NOW()'),
 * @property string $lastModified  defaultExpression('NOW()'),
 * @property int $createdBy
 * @property int $modifiedBy
 */
class Menu extends \yii\db\ActiveRecord
{
    /**
     * @inheritDoc
     *
     * @return string
     */
    public static function tableName(): string
    {
        return 'Menu';
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
            [['title', 'template'], 'string', 'max' => 255],
            [['title', 'template', 'published'], 'required'],
            [['published', 'modifiedBy', 'createdBy'], 'integer'],
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
            'title' => 'Title',
            'published' => 'Published',
            'template' => 'Template',
            'dateCreated' => 'Date Created',
            'lastModified' => 'Last Modified',
            'createdBy' => 'Created By',
            'modifiedBy' => 'Modified By',
        ];
    }
}
