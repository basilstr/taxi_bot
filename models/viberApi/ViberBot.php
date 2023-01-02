<?php

namespace app\models\viberApi;

use app\models\viberApi\Api\Event\Factory;
use app\models\viberApi\Api\Message\Contact;
use app\models\viberApi\Bot\Manager;
use app\models\viberApi\Api\Event;
use app\models\viberApi\Api\Event\Message;
use app\models\viberApi\Api\Message\Text;
use app\models\viberApi\Api\Message\File;
use app\models\viberApi\Api\Message\Location;
use app\models\viberApi\Api\Message\Picture;
use app\models\viberApi\Api\Event\Seen;
use app\models\viberApi\Api\Event\Subscribed;
use app\models\viberApi\Api\Event\Unsubscribed;
use app\models\viberApi\Api\Event\Conversation;
use app\models\BotUsers;
use app\models\viberApi\Api\Entity;


/**
 * Build bot with viber client
 *
 */
class ViberBot
{
    protected $id;
    protected $name;
    protected $client;

    /**
     * Event managers collection
     *
     * @var array
     */
    protected $managers = [];

    /**
     * Execute scenario
     */
    protected $scenario;

    /**
     * Init client
     *
     * @throws \RuntimeException
     * @param array $options
     */
    public function __construct($botDB)
    {
        if (isset($botDB->token)) {
            $this->id = $botDB->id;
            $this->name = $botDB->name;
            $this->client = new ViberClient($botDB->token);
        } else {
            throw new \RuntimeException('Specify "client" or "token" parameter');
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * Get current bot client
     *
     * @return ViberClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Register event handler callback
     *
     * @param \Closure $checker
     * @param \Closure $handler
     * @return $this
     */
    public function on(\Closure $checker, \Closure $handler)
    {
        $this->managers[] = new Manager($checker, $handler);

        return $this;
    }

    /**
     * Register text message handler by PCRE
     *
     * @param  string $regexp valid regular expression
     * @param  \Closure $handler event handler
     * @return ViberBot
     */
    public function onText(\Closure $handler)
    {
        $this->managers[] = new Manager(function (Event $event) {
            return (
                $event instanceof Message
                && $event->getMessage() instanceof Text
            );
        }, $handler);

        return $this;
    }

    public function onContact(\Closure $handler)
    {
        $this->managers[] = new Manager(function (Event $event) {
            return (
                $event instanceof Message
                && $event->getMessage() instanceof Contact
            );
        }, $handler);

        return $this;
    }

    public function onLocation(\Closure $handler)
    {
        $this->managers[] = new Manager(function (Event $event) {
            return (
                $event instanceof Message
                && $event->getMessage() instanceof Location
            );
        }, $handler);

        return $this;
    }

    /**
     * Register subscrive event handler
     *
     * @param  \Closure $handler valid function
     * @return ViberBot
     */
    public function onSubscribe(\Closure $handler)
    {
        $this->managers[] = new Manager(function (Event $event) {
            return ($event instanceof Subscribed);
        }, $handler);

        return $this;
    }

    /**
     * Register Unsubscribe event handler
     *
     * @param  \Closure $handler valid function
     * @return ViberBot
     */
    public function onUnsubscribe(\Closure $handler)
    {
        $this->managers[] = new Manager(function (Event $event) {
            return ($event instanceof Unsubscribed);
        }, $handler);

        return $this;
    }

    /**
     * Register conversation event handler
     *
     * @param \Closure $handler valid function
     * @return ViberBot
     */
    public function onConversation(\Closure $handler)
    {
        $this->managers[] = new Manager(function (Event $event) {
            return ($event instanceof Conversation);
        }, $handler);

        return $this;
    }

    /**
     * Register picture message handler
     *
     * @param \Closure $handler event handler
     * @return ViberBot
     */
    public function onPicture(\Closure $handler)
    {
        $this->managers[] = new Manager(function (Event $event) {
            return (
                $event instanceof Message
                && $event->getMessage() instanceof Picture
            );
        }, $handler);

        return $this;
    }

    public function onFile(\Closure $handler)
    {
        $this->managers[] = new Manager(function (Event $event) {
            return (
                $event instanceof Message
                && $event->getMessage() instanceof File
            );
        }, $handler);

        return $this;
    }

    /**
     * Register seen event handler
     *
     * @param \Closure $handler valid function
     * @return ViberBot
     */
    public function onSeen(\Closure $handler)
    {
        $this->managers[] = new Manager(function (Event $event) {
            return ($event instanceof Seen);
        }, $handler);

        return $this;
    }

    /**
     * Get bot input stream
     *
     * @return string
     */
    public function getInputBody()
    {
        return file_get_contents('php://input');
    }

    /**
     * Response with entity
     *
     * @param  Entity $entity
     * @return void
     */
    public function outputEntity(Entity $entity)
    {
        header('Content-Type: application/json');
        echo json_encode($entity->toApiArray());
    }

    public function run()
    {
        $eventBody = $this->getInputBody();
        // check json
        $eventBody = json_decode($eventBody, true, 512, JSON_BIGINT_AS_STRING);

        if (json_last_error() || empty($eventBody) || !is_array($eventBody)) {
            throw new \RuntimeException('Invalid json request', 3);
        }

        // make event from json
        $event = Factory::makeFromApi($eventBody);

        // main bot loop
        /** @var Manager $manager */
        foreach ($this->managers as $manager) {
            if ($manager->isMatch($event)) {
                $returnValue = $manager->runHandler($event);
                if ($returnValue && $returnValue instanceof Entity) { // reply with entity
                    $this->outputEntity($returnValue);
                }
                break;
            }
        }
        return $this;
    }

    /**
     * @param integer $chat_id
     * @return bool
     */
    public function unsubscribedBotUser($chat_id)
    {
        return BotUsers::setBotUnsubscribe($chat_id);
    }

    /**
     * @param integer $chat_id
     * @return bool
     */
    public function subscribedBotUser($chat_id)
    {
        return BotUsers::setBotSubscribe($chat_id);
    }

    public function errorProcessing($error)
    {
        $code = isset($error['code']) ? $error['code'] : 0;
        $receiver = isset($error['receiver']) ? $error['receiver'] : 0;
        //6 | receiverNotSubscribed | The receiver is not subscribed to the account
        if($code == 6) {
            $this->unsubscribedBotUser($receiver);
            return;
        }
    }

}
