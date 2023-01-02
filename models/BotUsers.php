<?php

namespace app\models;

use TheSeer\Tokenizer\Exception;
use Yii;
use app\models\viberApi\ViberBot;
use app\models\viberApi\Api\Event\Conversation;

/**
 * This is the model class for table "bot_users".
 *
 * @property int $id
 * @property int $id_bot посилання на бота, на який клієнт підписаний
 * @property string $chat_id chat_id клієнта
 * @property string $phone номер телефону клієнта
 * @property string $is_subscribe чи підписаний клієнт
 * @property int $current_type поточний тип клієнта (1- водій | 2 - пасажир)
 * @property string $current_dialog етапи діалогу і відповіді клієнта
 * @property string $params json строка сlient: імя | driver: номер авто, колір, марка
 * @property string $lat координати клієнта
 * @property string $lon координати клієнта
 * @property string $dt_coordinate дата оновлення координат
 * @property string $name имя клієнт в мессенджері
 * @property string $language яка мова встановлена у клієнта
 * @property string $avatar аватар клієнт
 * @property string $dt_subscribe дата, коли клієнт підписався
 * @property string $dt_last_action дата останньої активності клієнт
 * @property string $dt_ban до якої дати забанений користувач
 * @property int $admin_msg отримувати повідомлення, які пишуть користувачі 1-отримувати тільки без замовлення, 2-отримувати всі
 *
 * @property Bots $bot
 */
class BotUsers extends \yii\db\ActiveRecord
{
    const DRIVER = 1;
    const CLIENT = 2;

    public $currentDialog; //  current_dialog as ARRAY

    public $parameters; //  params as ARRAY

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bot_users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_bot'], 'required'],
            [['id_bot', 'is_subscribe', 'current_type', 'admin_msg'], 'integer'],
            [['dt_subscribe', 'dt_last_action', 'dt_coordinate', 'dt_ban'], 'safe'],
            [['chat_id', 'name'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 20],
            [['language'], 'string', 'max' => 5],
            [['avatar'], 'string', 'max' => 225],
            [['lat', 'lon'], 'number'],
            [['current_dialog'], 'string'],
            [['id_bot'], 'exist', 'skipOnError' => true, 'targetClass' => Bots::class, 'targetAttribute' => ['id_bot' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_bot' => 'ID Бота',
            'chat_id' => 'Чат ID',
            'phone' => 'Телефон',
            'is_subscribe' => 'Підписаний',
            'current_dialog' => 'Етапи діалога',
            'current_type' => 'Тип клієнта',
            'name' => 'Им\'я',
            'language' => 'Мова',
            'avatar' => 'Аватар',
            'dt_subscribe' => 'Дата підписки',
            'dt_last_action' => 'Активність',
            'dt_ban' => 'Забанений до',
            'admin_msg' => 'Отримувати',
        ];
    }

    public static function typeSubscribe()
    {
        return [
            0 => 'Не підписаний',
            1 => 'Підписаний',
        ];
    }

    public static function typeUser()
    {
        return [
            0 => 'Не визначено',
            1 => 'Водій',
            2 => 'Пасажир',
        ];
    }

    public static function getTypeUser($typeUser)
    {
        if($typeUser ==  null) return 'Не визначено';
        return BotUsers::typeUser()[$typeUser];
    }

    public static function typeAdminMsg()
    {
        return [
            0 => 'Не отримувати',
            1 => 'Поза замовленнями',
            2 => 'Вся переписка',
        ];
    }

    public function isViber()
    {
        return $this->bot->type == 'viber';
    }

    public function isTelegram()
    {
        return $this->bot->type == 'telegram';
    }

    public function isBanned()
    {
        return time() < strtotime($this->dt_ban);
    }

    public function getAvatar()
    {
        return $this->avatar ? $this->avatar : '/img/no_user.png';
    }


    public function afterFind()
    {
        parent::afterFind();

        if(empty($this->current_dialog)){
            $this->currentDialog = [];
        }else{
            $this->currentDialog = json_decode($this->current_dialog, true);
        }

        if(empty($this->params)){
            $this->parameters = [];
        }else{
            $this->parameters = json_decode($this->params, true);
        }

    }

    public function beforeSave($insert)
    {
        if(empty($this->currentDialog)){
            $this->current_dialog = null;
        }else{
            $this->current_dialog = json_encode($this->currentDialog, JSON_UNESCAPED_UNICODE);
        }

        if(empty($this->parameters)){
            $this->params = null;
        }else{
            $this->params = json_encode($this->parameters, JSON_UNESCAPED_UNICODE);
        }
        return parent::beforeSave($insert);
    }

    /**
     * @return \yii\db\ActiveQuery|Bots
     */
    public function getBot()
    {
        return $this->hasOne(Bots::class, ['id' => 'id_bot']);
    }

    /**
     * оновить дату последней активности
     * @return bool
     */
    public function updateLastAction()
    {
        $this->dt_last_action = date('Y-m-d H:i:s');
        if (!$this->save()) {
            Log::error('updateLastAction', 'bot_users.log');
            Log::error($this->errors, 'bot_users.log');
            return false;
        }
        return true;
    }

    /**
     * @param integer $chat_id
     * @return bool
     */
    public static function isBotUserPhone($chat_id)
    {
        $bot_user = self::find()->where(['chat_id' => $chat_id])->one();
        if ($bot_user) {
            return strlen($bot_user->phone) > 0;
        }
        return false;
    }

    /**
     * @param integer $chat_id
     * @return bool
     */
    public static function isBotUserSubscribe($chat_id)
    {
        $bot_user = self::find()->where(['chat_id' => $chat_id])->one();
        if ($bot_user) {
            return $bot_user->is_subscribe;
        }
        return false;
    }

    public static function setBotSubscribe($chat_id)
    {
        /** @var BotUsers $bot_user */
        $bot_user = self::find()->where(['chat_id' => $chat_id])->one();
        if ($bot_user) {
            // користувач вже підписаний
            if ($bot_user->is_subscribe == 1) return true;

            $bot_user->dt_last_action = date('Y-m-d H:i:s');
            $bot_user->dt_subscribe = date('Y-m-d H:i:s');
            $bot_user->is_subscribe = 1;
            if ($bot_user->save()) return true;

            Log::error('setBotSubscribe', 'bot_users.log', false);
            Log::error($bot_user->errors, 'bot_users.log');
            return false;
        }
        return true;
    }

    public static function setBotUnsubscribe($chat_id)
    {
        /** @var BotUsers $bot_user */
        $bot_user = self::find()->where(['chat_id' => $chat_id])->one();
        if ($bot_user) {
            // користувач вже відписаний
            if ($bot_user->is_subscribe == 0) return true;

            $bot_user->current_menu = 0;
            $bot_user->current_dialog = null;
            $bot_user->dt_last_action = date('Y-m-d H:i:s');
            $bot_user->is_subscribe = 0;
            if ($bot_user->save()) return true;
            Log::error('setBotUnsubscribe', 'bot_users.log', false);
            Log::error($bot_user->errors, 'bot_users.log');
            return false;
        }
        return true;
    }

    /**
     * @param ViberBot $bot
     * @param Conversation $event
     * @param integer $num_tonnel
     * @return bool
     */
    public static function saveBotUser($bot, $event, $phone = '')
    {
        $bot_user = self::find()->where(['chat_id' => $event->getUser()->getId()])->one();
        if (empty($bot_user)) {
            $bot_user = new self();
        }
        $bot_user->id_bot = $bot->getId();
        $bot_user->chat_id = $event->getUser()->getId();
        $bot_user->name = $event->getUser()->getName();
        $bot_user->is_subscribe = 0;
        $bot_user->current_menu = 0;
        $bot_user->current_dialog = null;
        $bot_user->current_type = null;
        if(strlen($bot_user->phone) == 0) $bot_user->phone = $phone;
        $bot_user->language = $event->getUser()->getLanguage();
        $bot_user->avatar = $event->getUser()->getAvatar();
        $bot_user->dt_last_action = date('Y-m-d H:i:s');

        if ($bot_user->save()) return $bot_user;

        Log::error('saveBotUser', 'bot_users.log');
        Log::error($bot_user->errors, 'bot_users.log');
        return false;
    }

    /**
     * @param integer $chat_id
     * @param string $phone
     * @return bool
     */
    public static function setBotUserPhone($chat_id, $phone)
    {
        $bot_user = self::find()->where(['chat_id' => $chat_id])->one();
        if ($bot_user) {
            $bot_user->phone = $phone;

            if ($bot_user->save()) {
                self::setBotSubscribe($chat_id);
                return true;
            }
            Log::error('setBotUserPhone', 'bot_users.log', false);
            Log::error($bot_user->errors, 'bot_users.log');
            return false;
        }
        return true;
    }

    /**
     * @param integer $chat_id
     * @param array $location
     * @return array|bool
     */
    public static function setBotUserLocation($chat_id, $location)
    {
        /** @var self $bot_user */
        $bot_user = self::find()->where(['chat_id' => $chat_id])->one();
        if ($bot_user) {
            if(isset($location['lat']) && floatval($location['lat'])>0 && isset($location['lon']) && floatval($location['lon'])>0) {
                $bot_user->lat = $location['lat'];
                $bot_user->lon = $location['lon'];
                $bot_user->dt_coordinate = date('Y-m-d H:i:s');
                if ($bot_user->save()) {
                    if(strtotime($bot_user->dt_ban)> time()){
                        $res = BotSending::sendText(0, $bot_user->id, \Yii::t('app', 'Можливість користування сервісом призупинена до ') . date('d.m.Y H:i', strtotime($bot_user->dt_ban)));
                        return true;
                    }
                    if ($bot_user->current_type == self::CLIENT) {
                        Order::addOrder($bot_user->id, $location['lat'], $location['lon']);
                    }else{
                        BotSending::sendText(0, $bot_user->id, \Yii::t('app', 'Ваші координати оновлено'));
                    }
                    return true;
                }
                Log::error('setBotUserLocation', 'bot_users.log', false);
                Log::error($bot_user->errors, 'bot_users.log');
                return false;
            }else{
                BotSending::sendText(0, $bot_user->id, \Yii::t('app', 'Ваші координати не коректні, спробуйте ще раз'));
            }
        }
        return true;
    }
}
