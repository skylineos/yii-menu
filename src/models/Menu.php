<?php

namespace skylineos\yii\menu\models;

use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * The model class for the table Menu
 *
 * @property int $id
 * @property string $title
 * @property int $status  defaultValue(1)
 * @property string $template  defaultValue('@vendor/skyline/yii-menu/views/menu/menu.php')
 * @property string $dateCreated  defaultExpression('NOW()'),
 * @property string $lastModified  defaultExpression('NOW()'),
 * @property int $createdBy
 * @property int $modifiedBy
 */
class Menu extends \yii\db\ActiveRecord
{
    public const STATUS_DELETED = -1;
    public const STATUS_UNPUBLISHED = 0;
    public const STATUS_PUBLISHED = 1;

    public const STATUS_TITLE = [
        self::STATUS_PUBLISHED => 'Published',
        self::STATUS_UNPUBLISHED => 'Unpublished',
        self::STATUS_DELETED => 'Deleted',
    ];

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
                'value' => new \yii\db\Expression('NOW()'),
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
            [['title', 'template', 'status'], 'required'],
            [['status', 'modifiedBy', 'createdBy'], 'integer'],
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
            'status' => 'Status',
            'template' => 'Template',
            'dateCreated' => 'Date Created',
            'lastModified' => 'Last Modified',
            'createdBy' => 'Created By',
            'modifiedBy' => 'Modified By',
        ];
    }
}
