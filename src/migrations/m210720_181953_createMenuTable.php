<?php

use yii\db\Migration;
use skylineos\yii\menu\models\Menu;

/**
 * Handles the creation of table `{{%Menu}}`.
 */
class m210720_181953_createMenuTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%Menu}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull()->unique(),
            'status' => $this->smallInteger()->notNull()->defaultValue(Menu::STATUS_PUBLISHED),
            'template' => $this->string()->notNull()->defaultValue('@vendor/skyline/yii-menu/views/menu/menu.php'),
            'dateCreated' => $this->datetime()->defaultExpression('NOW()'),
            'lastModified' => $this->datetime()->defaultExpression('NOW()'),
            'createdBy' => $this->integer()->unsigned()->notNull(),
            'modifiedBy' => $this->integer()->unsigned()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%Menu}}');
    }
}
