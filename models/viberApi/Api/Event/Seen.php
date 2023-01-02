<?php

namespace app\models\viberApi\Api\Event;

use app\models\viberApi\Api\Event;
use app\models\viberApi\Api\User;

/**
 * Triggers when message was seen
 *
 * @author Novikov Bogdan <hcbogdan@gmail.com>
 */
class Seen extends Event
{
    /**
     * Viber user id
     * @var string
     */
    protected $user_id;

    /**
     * Get the value of Viber user id
     *
     * @return string
     */
    public function getUserId()
    {
        return $this->user_id;
    }
}
