<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bot_viber_log".
 *
 * @property int $id
 * @property string|null $event подія
 * @property string|null $chat_id chat_id
 * @property string|null $id_message номер повідомлення в системі вайбера чи телеграма
 * @property string|null $text текст повідомлення
 * @property string $create_dt
 */
class BotViberLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bot_viber_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['text'], 'string'],
            [['create_dt'], 'safe'],
            [['event'], 'string', 'max' => 255],
            [['chat_id', 'id_message'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event' => 'Event',
            'chat_id' => 'Chat ID',
            'id_message' => 'Id Message',
            'text' => 'Text',
            'create_dt' => 'Create Dt',
        ];
    }

    public static function addViberLog($event, $chat_id, $id_message, $text)
    {
        if(self::find()->where(['id_message' => $id_message])->exists()) return false;
        $log = new self();
        $log->event = $event;
        $log->chat_id = $chat_id;
        $log->id_message = $id_message;
        $log->text = $text;
        if($log->save()){
            return true;
        }else{
            Log::error($log->errors, 'addViberLog.log');
        }
        return false;
    }
}
