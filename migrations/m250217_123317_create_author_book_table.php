<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%author_book}}`.
 */
class m250217_123317_create_author_book_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%author_book}}', [
            'author_id' => $this->integer()->notNull(),
            'book_id' => $this->integer()->notNull(),
        ]);

        $this->addPrimaryKey('author_book_pk', '{{%author_book}}', ['author_id', 'book_id']);

        $this->addForeignKey(
            'author_book_fk',
            '{{%author_book}}',
            'author_id',
            '{{%authors}}',
            'id',
            'CASCADE',
            'CASCADE',
        );

        $this->addForeignKey(
            'book_author_fk',
            '{{%author_book}}',
            'book_id',
            '{{%books}}',
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
        $this->dropTable('{{%author_book}}');
    }
}
