<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%books}}`.
 */
class m250217_123105_create_books_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%books}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'year' => $this->integer(),
            'description' => $this->text(),
            'isbn' => $this->string(13)->notNull()->unique(),
            'cover_img_path' => $this->string(255),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%books}}');
    }
}
