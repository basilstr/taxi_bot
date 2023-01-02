<?php

namespace app\models\viberApi\Api\Event;

use app\models\viberApi\Api\Event;
use app\models\viberApi\Api\Sender;

/**
 * Triggers when user send message
 *
 * @author Novikov Bogdan <hcbogdan@gmail.com>
 */
class Message extends Event
{
    /**
     * Who send message
     *
     * @var Sender
     */
    protected $sender;

    /**
     * Message data
     *
     * @var Message
     */
    protected $message;

    /**
     * Get the value of Who send message
     *
     * @return Sender
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Get the value of Message data
     *
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;
    }
}
