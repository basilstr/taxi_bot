<?php

use yii\db\Migration;

/**
 * Class m211008_160614_create_order
 */
class m211008_160614_create_order extends Migration
{
    public $table = 'order';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable($this->table, [
            'id' => $this->primaryKey(),
            'id_client' => $this->integer()->comment('хто створив замовлення')->null(),
            'id_driver' => $this->integer()->comment('хто виконує замовлення')->null(),
            'status' => $this->integer()->comment('статус замовлення')->null(),
            'address' => $this->text()->null()->comment('адреса замовлення, якщо вдалось встановити'),
            'lat' => $this->decimal(10,8)->null()->comment('координати замовлення'),
            'lon' => $this->decimal(11,8)->null()->comment('координати замовлення'),
            'create_dt' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->addCommentOnTable($this->table, 'замовлення');

        $this->createIndex('create_dt', $this->table, 'create_dt');
        $this->createIndex('status', $this->table, 'status');

        $this->addForeignKey(
            'FK_id_client',  // это "условное имя" ключа
            $this->table, // это название текущей таблицы
            'id_client', // это имя поля в текущей таблице, которое будет ключом
            'bot_users', // это имя таблицы, с которой хотим связаться
            'id', // это поле таблицы, с которым хотим связаться
            'CASCADE'
        );
        $this->addForeignKey(
            'FK_id_driver',  // это "условное имя" ключа
            $this->table, // это название текущей таблицы
            'id_driver', // это имя поля в текущей таблице, которое будет ключом
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
