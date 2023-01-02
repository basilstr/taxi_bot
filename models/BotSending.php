<?php

namespace app\models;

use app\models\viberApi\Api\Message\Location;
use Yii;
use app\models\viberApi\ViberBot;
use app\models\viberApi\Api\Sender;
use app\models\viberApi\Api\Message\Text;
use app\models\Scenario\BotMenu;
use app\models\viberApi\BotSendingViber;

/**
 *
 * Class BotSending
 * @package app\models
 */

class BotSending
{
    public static function sendText($id_user_sender, $id_user_receiver, $text)
    {
        /** @var BotUsers $botUser */
        $botUser = BotUsers::findOne($id_user_receiver);
        if(empty($botUser)) return false;
        if($botUser->isViber()) {
            $res = BotSendingViber::sendText($id_user_sender, $id_user_receiver, $text);
            BotMessages::addMessage($id_user_sender, $id_user_receiver, $text, $res->getMessageToken());
            return $res;
        }
        return false;
    }

    public static function sendLocation($id_user_sender, $id_user_receiver, $lat, $lon)
    {
        /** @var BotUsers $botUser */
        $botUser = BotUsers::findOne($id_user_receiver);
        if(empty($botUser)) return false;
        if($botUser->isViber()) {
            $res = BotSendingViber::sendLocation($id_user_sender, $id_user_receiver, $lat, $lon);
            $text = 'lat: ' . $lat . ' lon: ' . $lon;
            BotMessages::addMessage($id_user_sender, $id_user_receiver, $text, $res->getMessageToken());
            return $res;
        }
        return false;
    }
}