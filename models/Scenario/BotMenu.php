<?php

namespace app\models\Scenario;

use yii\base\Model;
use app\models\BotUsers;
use app\models\viberApi\ViberBot;
use app\models\viberApi\Api\Keyboard;
use app\models\viberApi\Api\Message\Text;
use app\models\BotSending;
use app\models\viberApi\Api\Message\CarouselContent;
use app\models\Order;
use app\models\viberApi\BotSendingViber;


class BotMenu extends Model
{
    /**
     * 0 - ідсутність початкового меню
     * 1 - я водій / я клієнт
     */
    /** @var  BotUsers $botUser */
    private $botUser;

    /**
     * @var ViberBot $bot
     */
    private $bot;

    private $botSender;

    private $message = '';

    /**
     * @param string $chat_id чат ID
     * @param string $message сообщение от пользователя чат бота
     * @param int $id_sender ID пользователя-отправителя. Если такой отсутствует - тогда системный пользователь
     * @return bool
     */
    public static function putMenu($chat_id, $message = '')
    {
        $botMenu = new self();
        $botMenu->botUser = BotUsers::findOne(['chat_id' => $chat_id]);
        if (empty($botMenu->botUser)) return false;
        if (empty($botMenu->botUser->bot)) return false;
        $botMenu->bot = new ViberBot($botMenu->botUser->bot);
        $botMenu->botSender = BotSendingViber::getSender(0);
        $botMenu->botUser->updateLastAction();

        // початкова ініціалізація меню
        $botMenu->initCurrentMenu();

        if($botMenu->botUser->isBanned()){
            $res = BotSending::sendText(0, $botMenu->botUser->id, \Yii::t('app', 'Можливість користування сервісом призупинена до ') . date('d.m.Y H:i', strtotime($botMenu->botUser->dt_ban)));
            return true;
        }

        // обробка діалогового сценарію
        if ($botMenu->parseDialog($message)) return true;
        // обробка текстових повідомлень
        if ($botMenu->parseMessage($message)) {
            return true;
        }
        // початок діалогу після натискання на кнопки, які передбачають початок діалогу
        if ($botMenu->parseDialog()) return true;
        switch ($botMenu->botUser->current_menu) {
            case 1:
                $botMenu->menuDriver();
                break;
            case 2:
                $botMenu->menuClient();
                break;
            default:
                $botMenu->menuStart();
        }

        return true;
    }

    // формування меню при відповіді системою (потрібно вивести повторно меню)
    public static function currentMenuButton($chat_id)
    {
        $botMenu = new self();
        $botMenu->botUser = BotUsers::findOne(['chat_id' => $chat_id]);
        if (empty($botMenu->botUser)) return false;
        if (empty($botMenu->botUser->bot)) return false;
        $botMenu->bot = new ViberBot($botMenu->botUser->bot);
        $botMenu->botSender = BotSendingViber::getSender(0);

        // початкова ініціалізація меню
        $botMenu->initCurrentMenu();

        switch ($botMenu->botUser->current_menu) {
            case 1:
                return BotButtonMenu::menuDriverButton();
            case 2:
                return BotButtonMenu::menuClientButton();
            default:
                return BotButtonMenu::menuStartButton();
        }
    }

    public static function putMenuInline($id_user, $listMenu, $id_sender = 0)
    {
        if (empty($listMenu)) return false;

        $botMenu = new self();
        $botMenu->botUser = BotUsers::findOne(['id' => $id_user]);
        if (empty($botMenu->botUser)) return false;
        if (empty($botMenu->botUser->bot)) return false;
        $botMenu->bot = new ViberBot($botMenu->botUser->bot);
        $botMenu->botSender = BotSendingViber::getSender($id_sender);

        $buttons = BotButtonMenu::MenuInlineButton($listMenu);
        $res = $botMenu->bot->getClient()->sendMessage(
            (new CarouselContent())
                ->setSender($botMenu->botSender)
                ->setReceiver($botMenu->botUser->chat_id)
                ->setButtonsGroupColumns(6)
                ->setButtonsGroupRows(count($buttons))
                ->setBgColor('#FFFFFF')
                ->setButtons($buttons)
        );
        return $res;
    }

    // початкова ініціалізація
    private function initCurrentMenu()
    {
        \Yii::$app->language = $this->botUser->select_language;
    }

    /**
     * @param string $message
     * @return bool
     */
    private function parseDialog($message = '')
    {
        if($this->botUser->currentDialog){
            // якщо нажата кнопка меню, то скасовується діалог (очищається в базі)
            if (strpos($message, '/btn') !== false) {
                $this->botUser->currentDialog = null;
                $this->botUser->save();
                return false;
            }
            $dialog = new BotDialog($this->botUser->currentDialog);
            if (!$dialog->validate($message)) {
                $res = BotSending::sendText(0, $this->botUser->id, $dialog->error);
                return true;
            }
            // если ответ прошел валидацию - сохраняем его в currentDialog
            $this->botUser->currentDialog = $dialog->currentDialog;
            $this->botUser->save();
            // виводим первый вопрос в диалоге, где еще нет ответа
            foreach ($this->botUser->currentDialog['questions'] as $currentDialog) {
                if(empty($currentDialog['answer'])) {
                    $res = BotSending::sendText(0, $this->botUser->id, $currentDialog['question']);
                    return true;
                }
            }
            $resDialog = $dialog->endDialog($this->botUser);
            $res = BotSending::sendText(0, $this->botUser->id, $resDialog);
            $this->botUser->currentDialog = null;
            $this->botUser->save();
            return true;
        }
        // диалога нет
        return false;
    }

    // парсинг сообщения на определения посланой комманды по нажатию кнопок
    /**
     * @param string $message
     * @return bool
     */
    private function parseMessage($message)
    {
        if(empty($message)) return false;
        $matches = null;

        // если нажата кнопка меню №1
        if (preg_match('#\/btn-menu-(\d+)#', $message, $matches)) {
            switch ($matches[1]) {
                case 0:
                    $this->actionStartMenu();
                    break;
                case 1:
                    $this->actionButtomIDriver();
                    break;
                case 2:
                    $this->actionButtonIClient();
                    break;
                case 3:
                    $this->actionButtonChangeCar();
                    break;
                case 4:
                    $this->actionButtonInstruction();
                    break;
            }

            return false;
        }

        if (preg_match('#\/btn-order-accept-(\d+)#', $message, $matches)) {
            $this->actionAcceptOrder($matches[1]);
            return true;
        }

        if (preg_match('#\/btn-order-reject-(\d+)#', $message, $matches)) {
            $this->actionRejectOrder($matches[1]);
            return true;
        }


        // поскольку в сообщении не было команд от кнопок, то считаем, что это сообщение от пользователя бота
        $this->message = $message;
        $this->actionСommunication();
        return true;
    }

    private function menuStart()
    {
        $this->bot->getClient()->sendMessage(
            (new Text())
                ->setSender($this->botSender)
                ->setReceiver($this->botUser->chat_id)
                ->setText(\Yii::t('app', 'Визначте свій статус'))
                ->setMinApiVersion(4)
                ->setKeyboard(BotButtonMenu::menuStartButton())
        );
    }

    private function menuDriver()
    {
        $this->bot->getClient()->sendMessage(
            (new Text())
                ->setSender($this->botSender)
                ->setReceiver($this->botUser->chat_id)
                ->setText(\Yii::t('app', 'Зробіть свій вибір'))
                ->setMinApiVersion(4)
                ->setKeyboard(BotButtonMenu::menuDriverButton())
        );
    }


    private function menuClient()
    {
        $this->bot->getClient()->sendMessage(
            (new Text())
                ->setSender($this->botSender)
                ->setReceiver($this->botUser->chat_id)
                ->setText(\Yii::t('app', 'Зробіть свій вибір'))
                ->setMinApiVersion(4)
                ->setKeyboard(BotButtonMenu::menuClientButton())
        );
    }


    public function actionStartMenu()
    {
        $this->botUser->current_menu = 0;
        $this->botUser->current_type = 0;
        $this->botUser->save();
    }

    public function actionButtomIDriver()
    {
        $this->botUser->current_menu = 1; // меню для водія
        $this->botUser->current_type = 1; // водій
        // якщо параметри машини вже є то діалог не генеруємо, якщо немає - то заповнюємо ті, які є
        if(!isset($this->botUser->parameters['driver']['brand']) || !isset($this->botUser->parameters['driver']['number']) ){
            $dialog = new BotDialog(BotDialog::driverCar());
            if (isset($this->botUser->parameters['driver']['brand'])) $dialog->setAttribute('brand', $this->botUser->parameters['driver']['brand']);
            if (isset($this->botUser->parameters['driver']['number'])) $dialog->setAttribute('number', $this->botUser->parameters['driver']['number']);
            $this->botUser->currentDialog = $dialog->currentDialog;
        }
        if(isset($this->botUser->parameters['driver']['brand']) && isset($this->botUser->parameters['driver']['number']) ){
            BotSending::sendText(0, $this->botUser->id, \Yii::t('app', 'Ваше авто:')."\n".$this->botUser->parameters['driver']['brand']."\n".$this->botUser->parameters['driver']['number']);
        }
        $this->botUser->save();
    }

    public function actionButtonIClient()
    {
        $this->botUser->current_menu = 2; // меню для водія
        $this->botUser->current_type = 2; // водій
        $this->botUser->save();
    }

    public function actionButtonChangeCar()
    {
        $dialog = new BotDialog(BotDialog::driverCar());
        $this->botUser->currentDialog = $dialog->currentDialog;
        $this->botUser->save();
    }

    public function actionButtonInstruction()
    {
        $intro= [];
        $intro[] = \Yii::t('app', '- Сервіс *М-taxi* повністю безкоштовний та без посередників!');
        $intro[] = \Yii::t('app', '- *ВАЖЛИВО для пасажира* При створенні замовлення ДОЧЕКАЙТЕСЬ визначення координат !!!');
        $intro[] = \Yii::t('app', '- *ВАЖЛИВО для водія* замовлення пропонується в радіусі 10 км, тому ВАЖЛИВО натиснути кнопку "КООРДИНАТИ", щоб система змогла запропонувати вам замовлення');
        BotSending::sendText(0, $this->botUser->id, implode("\n\n", $intro));
    }

    public function actionAcceptOrder($id_order)
    {
        if(Order::forbiddenAccept($this->botUser->id)){
            BotSending::sendText(0, $this->botUser->id, \Yii::t('app', 'Ви вже виконуєте замовлення. Ви не можете взяти ішне протягом 10 хвилин'));
            return true;
        }
        /** @var Order $order */
        $order = Order::findOne($id_order);
        if (empty($order)) {
            BotSending::sendText(0, $this->botUser->id, \Yii::t('app', 'замовлення відсутнє'));
            return true;
        }
        if ($order->id_driver > 0) {
            BotSending::sendText(0, $this->botUser->id, \Yii::t('app', 'замовлення вже прийняте'));
            return true;
        }
        if ($order->status == Order::REJECT_CLIENT) {
            BotSending::sendText(0, $this->botUser->id, \Yii::t('app', 'замовлення скасоване пасажиром'));
            return true;
        }
        if ($order->status == Order::REJECT_DRIVER) {
            BotSending::sendText(0, $this->botUser->id, \Yii::t('app', 'замовлення скасоване водієм'));
            return true;
        }
        if ($order->status == Order::NOT_FOUND) {
            BotSending::sendText(0, $this->botUser->id, \Yii::t('app', 'замовлення вже пропонувалось водіям, проте дожен водій не відгукнувся на нього'));
            return true;
        }

        $order->id_driver = $this->botUser->id;
        $order->status = Order::ISSUED;
        $order->save();
        BotSending::sendLocation(0, $this->botUser->id, $order->lat, $order->lon);
        // вайбер сам робить зворотню геолокацію
        if ($order->address && $this->botUser->bot->type != 'viber') BotSending::sendText($order->id_client, $this->botUser->id, $order->address);

        // даємо можливість скасувати замовлення
        $menu = [
            ['btn' => '/btn-order-reject-' . $order->id, 'text' => \Yii::t('app', 'СКАСУВАТИ ЗАМОВЛЕННЯ')],
        ];

        BotMenu::putMenuInline($this->botUser->id, $menu, 0);
        BotSending::sendText(0, $this->botUser->id, \Yii::t('app', "*ЗАМОВЛЕННЯ ВИДАНО ВАМ*\n *ПАСАЖИР ЧЕКАЄ НА ВАС*\nВ цьому чаті ви можете спілкуватись з пасажиром"));

        $car = $this->botUser->parameters;
        $car_text = isset($car['driver']['brand']) ? $car['driver']['brand'].' ' : '';
        $car_text .= isset($car['driver']['number']) ? $car['driver']['number'] : '';
        BotSending::sendText(0, $order->id_client, \Yii::t('app', 'Замовлення прийняте. Очікуйте автомобіль '). $car_text."\n".\Yii::t('app', 'В цьому чаті ви можете спілкуватись з водієм'));
        // даємо можливість скасувати замовлення
        $menu = [
            ['btn' => '/btn-order-reject-' . $order->id, 'text' => \Yii::t('app', 'СКАСУВАТИ ЗАМОВЛЕННЯ')],
        ];

        BotMenu::putMenuInline($order->id_client, $menu, 0);
        BotSending::sendText(0, $order->id_client, \Yii::t('app', 'Якщо вам вже не потрібне авто - скасуйте замовлення'));
        return true;
    }

    public function actionRejectOrder($id_order)
    {
        /** @var Order $order */
        $order = Order::findOne($id_order);
        if (empty($order)) {
            BotSending::sendText(0, $this->botUser->id, \Yii::t('app', 'замовлення відсутнє'));
            return true;
        }

        if($this->botUser->current_type == BotUsers::DRIVER ){
            $order->status = Order::REJECT_DRIVER;
            BotSending::sendText(0, $order->id_driver, \Yii::t('app', "замовлення скасовано"));
            BotSending::sendText(0, $order->id_client, \Yii::t('app', 'Замовлення скасоване водієм. Якщо вам ще потрібне авто, спробуйте викликати авто ще раз'));
        }else{
            $order->status = Order::REJECT_CLIENT;
            BotSending::sendText(0, $order->id_driver, \Yii::t('app', "замовлення скасовано пасажиром"));
            BotSending::sendText(0, $order->id_client, \Yii::t('app', 'Замовлення скасоване.'));
        }

        $order->save();
        return true;
    }

    /**
     * спілкування між водієм та пасажиром
     */
    private function actionСommunication()
    {
        /** @var Order $order */
        $order = Order::find()
            ->where(['OR',
                ['id_client' => $this->botUser->id],
                ['id_driver' => $this->botUser->id],
            ])
            ->andWhere(['status' => Order::ISSUED])
            ->andWhere('create_dt > (NOW() - INTERVAL 15 MINUTE)')
            ->orderBy('create_dt DESC')
            ->one();

        if($order) {
            if ($order->id_client == $this->botUser->id) {
                BotSending::sendText($order->id_client, $order->id_driver, $this->message);
            } else {
                BotSending::sendText($order->id_driver, $order->id_client, $this->message);
            }
        }

        // відправлення повідомлення адміністраторам
        $users = BotUsers::find()->where(['>', 'admin_msg', 0])->all();
        /** @var BotUsers $user */
        foreach ($users as $user) {
            if ($user->admin_msg == 1 && empty($order)) BotSending::sendText(0, $user->id, $user->name . "\n" .$user->phone . "\n" . $this->message);
            if ($user->admin_msg == 2) BotSending::sendText(0, $user->id, $user->name . "\n" . $user->phone . "\n" . $this->message);
        }
    }
}