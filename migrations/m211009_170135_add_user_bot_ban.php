<?php

use yii\db\Migration;

/**
 * Class m211009_170135_add_user_bot_ban
 */
class m211009_170135_add_user_bot_ban extends Migration
{
    public $table = 'bot_users';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->table, 'dt_ban', $this->timestamp()->comment('до якої дати забанений користувач')->null());
        $this->createIndex('dt_ban',$this->table,'dt_ban');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->table, 'dt_ban');
    }
}
