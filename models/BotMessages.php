<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bot_messages".
 *
 * @property int $id
 * @property int|null $id_sender хто відправник повідомлення id пользователя
 * @property int|null $id_receiver хто oтримувач повідомлення id пользователя
 * @property string|null $text текст повідомлення
 * @property int|null $readed прочитано чи ні
 * @property string|null $id_message номер повідомлення в системі вайбера чи телеграма
 * @property string $create_dt
 *
 * @property BotUsers $userReceiver
 * @property BotUsers $userSender
 */

class BotMessages extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bot_messages';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_sender', 'id_receiver', 'readed'], 'integer'],
            [['text'], 'string'],
            [['create_dt'], 'safe'],
            [['id_message'], 'string', 'max' => 255],
            [['id_receiver'], 'exist', 'skipOnError' => true, 'targetClass' => BotUsers::class, 'targetAttribute' => ['id_receiver' => 'id']],
            [['id_sender'], 'exist', 'skipOnError' => true, 'targetClass' => BotUsers::class, 'targetAttribute' => ['id_sender' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_sender' => 'Id User Sender',
            'id_receiver' => 'Id User Recv',
            'text' => 'Text',
            'readed' => 'Readed',
            'id_message' => 'Id Message',
            'create_dt' => 'Create Dt',
        ];
    }

    /**
     * Gets query for [[UserRecv]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserReceiver()
    {
        return $this->hasOne(BotUsers::class, ['id' => 'id_receiver']);
    }

    /**
     * Gets query for [[UserSender]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserSender()
    {
        return $this->hasOne(BotUsers::class, ['id' => 'id_sender']);
    }

    public static function setReaded($chat_id)
    {
        /** @var BotUsers $bot_user */
        $bot_user = BotUsers::find()->where(['chat_id' => $chat_id])->one();
        $listMessageUnRead = self::find()->where(['readed' => 0, 'id_receiver' => $bot_user->id])->all();
        foreach ($listMessageUnRead as $messageUnRead) {
            $messageUnRead->readed = 1;
            $messageUnRead->save();
        }
    }

    public static function getMessageUser($id_user)
    {
        $listMessages = self::find()
            ->where(['OR',
                ['id_sender' => $id_user],
                ['id_receiver' => $id_user],
            ])
            ->orderBy('create_dt DESC')
            ->all();
        return $listMessages;
    }

    public static function getMessagePeriod($start_dt, $end_dt)
    {
        $listMessages = self::find()
            ->where(['>=', 'create_dt', $start_dt])
            ->andWhere(['<=', 'create_dt', $end_dt])
            ->orderBy('create_dt DESC')
            ->all();
        return $listMessages;
    }

    public static function addMessage($id_sender, $id_receiver, $text, $id_message)
    {
        if (empty($id_message)) return false;

        $msg = new self();
        $msg->id_sender = $id_sender > 0 ? $id_sender : null;
        $msg->id_receiver = $id_receiver > 0 ? $id_receiver : null;
        $msg->text = $text;
        $msg->id_message = $id_message;
        $msg->create_dt = date('Y-m-d H:i:s');
        if ($msg->save()) {
            return $msg;
        }
        Log::error($text, 'bot_list_messages.log', false);
        Log::error($msg->errors, 'bot_list_messages.log');
        return false;
    }
}
