<?php

namespace app\models\viberApi;

use app\models\viberApi\Api\Message\Location;
use Yii;
use app\models\viberApi\Api\Sender;
use app\models\viberApi\Api\Message\Text;
use app\models\Scenario\BotMenu;
use app\models\BotUsers;

/**
 *
 * Class BotSendingViber
 * @package app\models
 */

class BotSendingViber
{
    public static function getSender($id_user_sender)
    {
        /** @var BotUsers $user */
        $user = BotUsers::findOne($id_user_sender);
        if (empty($user)) {
            $properties['name'] = 'taxi-M';
            $properties['avatar'] = 'http://taxi-m.pp.ua/img/botLogo.png';
        }else{
            $properties['name'] = $user->name;
            $properties['avatar'] = $user->avatar;
        }
        return new Sender($properties);
    }

    public static function sendText($id_user_sender, $id_user_receiver, $text)
    {
        /** @var BotUsers $botUser */
        $botUser = BotUsers::findOne($id_user_receiver);
        if(empty($botUser)) return false;
        /** @var Sender $botSender */
        $bot = new ViberBot($botUser->bot);
        $botSender = self::getSender($id_user_sender);
        $menuButton = BotMenu::currentMenuButton($botUser->chat_id);
        $text = (new Text())
            ->setMinApiVersion(7)
            ->setSender($botSender)
            ->setReceiver($botUser->chat_id)
            ->setText($text);
        if ($menuButton) $text->setKeyboard($menuButton);
        $res = $bot->getClient()->sendMessage($text);
        if($res->isError()) {
            $bot->errorProcessing($res->getError());
        }
        return $res;
    }

    public static function sendLocation($id_user_sender, $id_user_receiver, $lat, $lon)
    {
        /** @var BotUsers $botUser */
        $botUser = BotUsers::findOne($id_user_receiver);
        if(empty($botUser)) return false;
        /** @var Sender $botSender */
        $bot = new ViberBot($botUser->bot);
        $botSender = self::getSender($id_user_sender);
        $text = (new Location())
            ->setSender($botSender)
            ->setReceiver($botUser->chat_id)
            ->setLat($lat)
            ->setLng($lon);
        $res = $bot->getClient()->sendMessage($text);
        if($res->isError()) {
            $bot->errorProcessing($res->getError());
        }
        return $res;
    }

}