<?php

use yii\db\Migration;

/**
 * Class m211011_163648_bot_viber_log
 */
class m211011_163648_bot_viber_log extends Migration
{
    public $table = 'bot_viber_log';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable($this->table, [
            'id' => $this->primaryKey(),
            'event' => $this->string(255)->comment('подія')->null(),
            'chat_id' => $this->string(32)->comment('chat_id')->null(),
            'id_message' => $this->string(32)->comment('номер повідомлення в системі вайбера чи телеграма')->null(),
            'text' => $this->text()->comment('текст повідомлення')->null(),
            'create_dt' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->addCommentOnTable($this->table, 'логування даних, які приходять з вайбера');

        $this->createIndex('id_message', $this->table, 'id_message');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->table);
    }
}
