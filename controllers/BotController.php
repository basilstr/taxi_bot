<?php

namespace app\controllers;

use yii\web\Controller;
use app\models\viberApi\ViberBot;
use app\models\Bots;
use app\models\viberApi\Api\Keyboard;
use app\models\viberApi\Api\Event;
use app\models\BotUsers;
use app\models\Log;
use app\models\Scenario\BotMenu;
use app\models\Scenario\Scenario;
use app\models\BotMessages;
use app\models\viberApi\BotSendingViber;
use app\models\BotViberLog;
use app\models\BotSending;

class BotController extends Controller
{
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionIndex($index = 0)
    {
        /** @var Bots $botDB */
        $botDB = Bots::findOne($index);
        if(empty($botDB)) return 'bot not found';
        if($botDB->type == 'viber') $this->viberBot($botDB);
        die();
    }

    public function actionUrl($index = 0)
    {
        // https://bit.ly/3v818WD
        /** @var Bots $botDB */
        $botDB = Bots::findOne($index);
        if(empty($botDB)) return 'bot not found';
        echo '<a href="'.$botDB->url.'"> ПІДПИСАТИСЬ </a>';
        die();
    }

    public function actionSendMessage()
    {
        if (\Yii::$app->request->isAjax) {
            $id_user = \Yii::$app->request->post('id_user', 0);
            $msg= \Yii::$app->request->post('msg');
            BotSending::sendText(0, $id_user, $msg);
            return 'ok';
        }
    }

    private function viberBot($botDB)
    {
        $bot = new ViberBot($botDB);
        $botSender = BotSendingViber::getSender(0);
        $bot->onConversation(function ($event) use ($bot, $botSender) {
            if(BotViberLog::addViberLog('onConversation', $event->getUser()->getId(), $event->getMessageToken(), '')) {
                Log::error('onConversation ===============> ', 'bot_controller.log');
                Log::error($event, 'bot_controller.log');
                if (BotUsers::saveBotUser($bot, $event, '')) {
                    $text = 'Вітаю.  Я бот taxi-M. Допоможу в пошуку авто. Для плідної співпраці поділись номером телефону натиснувши ПІДПИСАТИСЬ';
                    Scenario::subscribeWithPhone($bot, $botSender, $event->getUser()->getId(), $text, 'ПІДПИСАТИСЬ');
                }
            }
        })
            // получение номера телефона
            ->onContact(function ($event) use ($bot, $botSender) {
                if(BotViberLog::addViberLog('onContact', $event->getSender()->getId(), $event->getMessageToken(), $event->getMessage()->getPhoneNumber())) {
                    Log::error('onContact ===============> ', 'bot_controller.log', false);
                    Log::error($event, 'bot_controller.log');
                    if (BotUsers::setBotUserPhone($event->getSender()->getId(), $event->getMessage()->getPhoneNumber())) {
                        BotMenu::putMenu($event->getSender()->getId(), '');
                    }
                }
            })
            // получение географических координат
            ->onLocation(function ($event) use ($bot, $botSender) {
                if(BotViberLog::addViberLog('onLocation', $event->getSender()->getId(), $event->getMessageToken(), json_encode($event->getMessage()->getLocation(), JSON_UNESCAPED_UNICODE))) {
                    Log::error('onLocation ===============> ', 'bot_controller.log');
                    Log::error($event, 'bot_controller.log');
                    BotUsers::setBotUserLocation($event->getSender()->getId(), $event->getMessage()->getLocation());
                }
            })
            ->onText(function ($event) use ($bot, $botSender) {
                if(BotViberLog::addViberLog('onText', $event->getSender()->getId(), $event->getMessageToken(), $event->getMessage()->getText())) {
                    Log::error('onText ===============> ', 'bot_controller.log', false);
                    Log::error($event, 'bot_controller.log');
                    if (!BotUsers::isBotUserPhone($event->getSender()->getId())) {
                        $text = 'Шкода, але ви не надали свій номер телефону. Якщо ви це зробили помилково, то давайте спробуємо знову';
                        Scenario::subscribeWithPhone($bot, $botSender, $event->getUser()->getId(), $text, 'НАДАТИ ТЕЛЕФОН');
                    } else {
                        BotMenu::putMenu($event->getSender()->getId(), $event->getMessage()->getText());
                    }
                }
            })
            ->onPicture(function ($event) use ($bot, $botSender) {
                $data['text'] = $event->getMessage()->getText();
                $data['fileName'] = $event->getMessage()->getFileName();
                $data['media'] = $event->getMessage()->getMedia();
                if(BotViberLog::addViberLog('onPicture', $event->getSender()->getId(), $event->getMessageToken(),  json_encode($data, JSON_UNESCAPED_UNICODE))) {
                    Log::error('onPicture ===============> ', 'bot_controller.log', false);
                    Log::error($event, 'bot_controller.log');
                }
            })
            ->onFile(function ($event) use ($bot, $botSender) {
                $data['fileName'] = $event->getMessage()->getFileName();
                $data['media'] = $event->getMessage()->getMedia();
                if(BotViberLog::addViberLog('onFile', $event->getSender()->getId(), $event->getMessageToken(),  json_encode($data, JSON_UNESCAPED_UNICODE))) {
                    Log::error('onFile ===============> ', 'bot_controller.log', false);
                    Log::error($event, 'bot_controller.log');
                }
            })
            ->onSeen(function ($event) use ($bot, $botSender) {
                if(BotViberLog::addViberLog('onSeen', $event->getUserId(), $event->getMessageToken(),  '')) {
                    Log::error('onSeen ===============> ', 'bot_controller.log', false);
                    Log::error($event, 'bot_controller.log');
                    BotMessages::setReaded($event->getUserId());
                }
            })
            ->onSubscribe(function ($event) use ($bot, $botSender) {
                if(BotViberLog::addViberLog('onSubscribe', $event->getUser()->getId(), $event->getMessageToken(),  '')) {
                    Log::error('onSubscribe ===============> ', 'bot_controller.log');
                    Log::error($event, 'bot_controller.log');
                    $bot->subscribedBotUser($event->getUser()->getId());
                }
            })
            ->onUnsubscribe(function ($event) use ($bot, $botSender) {
                if(BotViberLog::addViberLog('onUnsubscribe', $event->getUserId(), $event->getMessageToken(),  '')) {
                    Log::error('onUnsubscribed ===============> ', 'bot_controller.log');
                    Log::error($event, 'bot_controller.log');
                    $bot->unsubscribedBotUser($event->getUserId());
                }
            })
            ->on(function ($event) {
                return true; // match all
            }, function ($event) {
                Log::error('Other event ===============> ', 'bot_controller.log', false);
                Log::error($event, 'bot_controller.log');
            })
            ->run();
    }
}
