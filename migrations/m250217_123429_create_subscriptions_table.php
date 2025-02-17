<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%subscriptions}}`.
 */
class m250217_123429_create_subscriptions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%subscriptions}}', [
            'id' => $this->primaryKey(),
            'author_id' => $this->integer()->notNull(),
            'phone_number' => $this->string()->notNull(),
        ]);

        $this->addForeignKey(
            'author_subscription_fk',
            '{{%subscriptions}}',
            'author_id',
            '{{%authors}}',
            'id',
            'CASCADE',
            'CASCADE',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%subscriptions}}');
    }
}
