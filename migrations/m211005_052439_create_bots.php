<?php

use yii\db\Migration;

/**
 * Class m211005_052439_create_bots
 */
class m211005_052439_create_bots extends Migration
{
    public $table = 'bots';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable($this->table, [
            'id' => $this->primaryKey(),
            'token' => $this->string(255)->comment('токен для бота')->null(),
            'type' => $this->string(255)->comment('тип бота viber | telegram')->null(),
            'webhook' => $this->boolean()->defaultValue(boolval(0))->comment('факт встановлення webhook для бота')->null(),
            'name' => $this->string(255)->comment('назва бота')->null(),
            'url' => $this->string(255)->comment('url на підписку')->null(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->addCommentOnTable( $this->table, 'Перелік ботів');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->table);
    }

}
