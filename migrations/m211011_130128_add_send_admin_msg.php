<?php

use yii\db\Migration;

/**
 * Class m211011_130128_add_send_admin_msg
 */
class m211011_130128_add_send_admin_msg extends Migration
{
    public $table = 'bot_users';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->table, 'admin_msg', $this->smallInteger()->defaultValue(0)->comment('отримувати повідомлення, які пишуть користувачі 1-отримувати тільки без замовлення, 2-отримувати всі'));
        $this->createIndex('admin_msg',$this->table,'admin_msg');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->table, 'admin_msg');
    }
}
