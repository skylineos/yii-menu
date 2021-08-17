<?php

use yii\db\Migration;

/**
 * Class m210817_144742_addTemplate_to_MenuItem
 */
class m210817_144742_addTemplate_to_MenuItem extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('MenuItem', 'template', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('MenuItem', 'template');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210817_144742_addTemplate_to_MenuItem cannot be reverted.\n";

        return false;
    }
    */
}
