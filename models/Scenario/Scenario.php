<?php
namespace app\models\Scenario;

use app\models\viberApi\ViberBot;
use app\models\viberApi\Api\Sender;
use app\models\viberApi\Api\Keyboard;
use app\models\viberApi\Api\Keyboard\Button;
use app\models\viberApi\Api\Message\Text;
use app\models\BotUsers;


class Scenario
{
    /**
     * @param ViberBot $bot
     * @param Sender $botSender
     * @param string $chat_id
     * @return bool
     */
    public static function subscribeWithPhone($bot, $botSender, $chat_id, $text, $buttonText)
    {
        $res = $bot->getClient()->sendMessage(
            (new Text())
                ->setSender($botSender)
                ->setReceiver($chat_id)
                ->setText($text)
                ->setMinApiVersion(7)
                ->setKeyboard((new Keyboard())
                    ->setBgColor('#ffffff')
                    ->setButtons([
                        (new Button())
                            ->setSilent(true)
                            ->setBgColor('#2fa4e7')
                            ->setTextHAlign('center')
                            ->setActionType('share-phone')
                            ->setActionBody('phone')
                            ->setTextSize('regular')
                            ->setImage('https://taxi-m.pp.ua/img/btn_6.png')
                            ->setText($buttonText)
                    ])
                ));
        //BotListMessages::addMessage($chat_id, $text, Users::SYSTEM_USER_ID, 'text', $res->getMessageToken(), '');
        return true;
    }

    /**
     * @param ViberBot $bot
     * @param Sender $botSender
     * @param string $chat_id
     * @return bool
     */
    public static function getLocation($bot, $botSender, $chat_id)
    {
        /** @var BotUsers $botUser */
        $botUser = BotUsers::findOne(['chat_id' => $chat_id]);
        if(empty($botUser)) return false;
        $text = "Натисніть на кнопку:\n\n \"КООРДИНАТИ\"";
        if($botUser->current_type == 1) $text = "Для того замовлення було якомога ближче до вас, натисніть на кнопку: КООРДИНАТИ";
        if($botUser->current_type == 2) $text = "Для того щоб водій знав куди подати машину, натисніть на кнопку: КООРДИНАТИ";
        $res = $bot->getClient()->sendMessage(
            (new Text())
                ->setSender($botSender)
                ->setReceiver($chat_id)
                ->setText($text)
                ->setMinApiVersion(7)
                ->setKeyboard(
                    (new Keyboard())
                        ->setBgColor('#ffffff')
                        ->setButtons([
                            (new Button())
                                ->setSilent(true)
                                ->setBgColor('#2fa4e7')
                                ->setTextHAlign('center')
                                ->setActionType('location-picker')
                                ->setActionBody('location')
                                ->setTextSize('regular')
                                ->setImage('https://taxi-m.pp.ua/img/btn_6.png')
                                ->setText('КООРДИНАТИ')
                        ])
                )
        );
        //BotListMessages::addMessage($chat_id, $text, Users::SYSTEM_USER_ID, 'text', $res->getMessageToken(), '');
        return true;
    }

}