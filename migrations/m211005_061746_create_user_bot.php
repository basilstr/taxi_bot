<?php

use yii\db\Migration;

/**
 * Class m211005_061746_create_user_bot
 */
class m211005_061746_create_user_bot extends Migration
{
    public $table = 'bot_users';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable($this->table, [
            'id' => $this->primaryKey(),
            'id_bot' => $this->integer()->comment('посилання на бота, на який клієнт підписаний')->notNull(),
            'chat_id' => $this->string(255)->comment('chat_id клієнта')->null(),
            'phone' => $this->string(20)->comment('номер телефону клієнта')->null(),
            'is_subscribe' => $this->boolean()->defaultValue(boolval(0))->comment('чи підписаний клієнт')->null(),
            'current_type' => $this->tinyInteger(1)->null()->comment('поточний тип клієнта (пасажир / водій)')->null(),
            'current_menu' => $this->tinyInteger()->null()->comment('номер набору кнопок меню')->null(),
            'current_dialog' => $this->text()->null()->comment('етапи діалогу і відповіді клієнта'),
            'params' => $this->text()->null()->comment('json строка сlient: імя | driver: номер авто, колір, марка'),
            'lat' => $this->decimal(10,8)->null()->comment('координати клієнта'),
            'lon' => $this->decimal(11,8)->null()->comment('координати клієнта'),
            'dt_coordinate' => $this->timestamp()->comment('дата оновлення координат')->null(),
            'name' => $this->string(255)->comment('имя клієнта в мессенджері')->null(),
            'language' => $this->string(5)->comment('яка мова встановлена у клієнта')->null(),
            'select_language' => $this->string(5)->comment('вибрана мова користувачем через меню')->defaultValue('uk'),
            'avatar' => $this->string(225)->comment('аватар клієнта')->null(),
            'dt_subscribe' => $this->timestamp()->comment('дата, коли клієнт підписався')->null(),
            'dt_last_action' => $this->timestamp()->comment('дата останньої активності клієнта')->null(),
        ], $tableOptions);

        $this->addCommentOnTable( $this->table, 'Користувачі ботів');

        $this->createIndex('chat_id',$this->table,'chat_id');
        $this->createIndex('phone',$this->table,'phone');
        $this->createIndex('dt_last_action',$this->table,'dt_last_action');
        $this->createIndex('dt_coordinate',$this->table,'dt_coordinate');

        $this->addForeignKey(
            'FK_id_bot',  // это "условное имя" ключа
            $this->table, // это название текущей таблицы
            'id_bot', // это имя поля в текущей таблице, которое будет ключом
            'bots', // это имя таблицы, с которой хотим связаться
            'id', // это поле таблицы, с которым хотим связаться
            'CASCADE'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK_id_bot', $this->table);
        $this->dropTable($this->table);
    }
}
