<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%MenuItem}}`.
 */
class m210720_192049_createMenuItemTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%MenuItem}}', [
            'id' => $this->primaryKey(),
            'menuId' => $this->integer()->notNull(),
            'parentItemId' => $this->integer()->null(),
            'title' => $this->string()->notNull(),
            'linkTo' => $this->string(),
            'linkTarget' => $this->string(),
            'sortOrder' => $this->integer()->notNull()->unsigned(),
            'dateCreated' => $this->datetime()->defaultExpression('NOW()'),
            'lastModified' => $this->datetime()->defaultExpression('NOW()'),
            'createdBy' => $this->integer()->unsigned()->notNull(),
            'modifiedBy' => $this->integer()->unsigned()->notNull(),
        ]);

        $this->addForeignKey(
            'fk-MenuItem_menuId-Menu_id',
            'MenuItem',
            'menuId',
            'Menu',
            'id',
            'CASCADE',
            'NO ACTION'
        );

        $this->addForeignKey(
            'fk-MenuItem_parentItemId-MenuItem_id',
            'MenuItem',
            'parentItemId',
            'MenuItem',
            'id',
            'CASCADE',
            'NO ACTION'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-MenuItem_menuId-Menu_id', 'MenuItem');
        $this->dropForeignKey('fk-MenuItem_parentItemId-MenuItem_id', 'MenuItem');
        $this->dropTable('{{%MenuItem}}');
    }
}
