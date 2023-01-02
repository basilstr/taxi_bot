<?php

namespace app\models\viberApi\Api\Event;

use app\models\viberApi\Api\Event;
use app\models\viberApi\Api\User;

/**
 * Triggers when user clicks a subscribe button
 *
 * @author Novikov Bogdan <hcbogdan@gmail.com>
 */
class Subscribed extends Event
{
    /**
     * Viber user
     * @var User
     */
    protected $user;

    /**
     * Get the value of Viber user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}
