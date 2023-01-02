<?php

use yii\db\Migration;

/**
 * Class m211006_105219_create_bot_messages
 */
class m211006_105219_create_bot_messages extends Migration
{
    public $table = 'bot_messages';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable($this->table, [
            'id' => $this->primaryKey(),
            'id_sender' => $this->integer()->comment('хто відправник повідомлення id пользователя')->null(),
            'id_receiver' => $this->integer()->comment('хто oтримувач повідомлення id пользователя')->null(),
            'text' => $this->text()->comment('текст повідомлення')->null(),
            'readed' => $this->boolean()->defaultValue(boolval(0))->comment('прочитано чи ні'),
            'id_message' => $this->string(255)->comment('номер повідомлення в системі вайбера чи телеграма')->null(),
            'create_dt' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->addCommentOnTable($this->table, 'переписка з користувачами');

        $this->createIndex('create_dt', $this->table, 'create_dt');
        $this->createIndex('id_message', $this->table, 'id_message');

        $this->addForeignKey(
            'FK_id_user_sender',  // это "условное имя" ключа
            $this->table, // это название текущей таблицы
            'id_sender', // это имя поля в текущей таблице, которое будет ключом
            'bot_users', // это имя таблицы, с которой хотим связаться
            'id', // это поле таблицы, с которым хотим связаться
            'CASCADE'
        );
        $this->addForeignKey(
            'FK_id_user_receiver',  // это "условное имя" ключа
            $this->table, // это название текущей таблицы
            'id_receiver', // это имя поля в текущей таблице, которое будет ключом
            'bot_users', // это имя таблицы, с которой хотим связаться
            'id', // это поле таблицы, с которым хотим связаться
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK_id_user_sender', $this->table);
        $this->dropForeignKey('FK_id_user_receiver', $this->table);
        $this->dropTable($this->table);
    }
}
