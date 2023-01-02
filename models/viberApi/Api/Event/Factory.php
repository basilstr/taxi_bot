<?php

namespace app\models\viberApi\Api\Event;

use app\models\viberApi\Exception\ApiException;
use app\models\viberApi\Api\Event;

/**
 * Event factory
 *
 * @author Novikov Bogdan <hcbogdan@gmail.com>
 */
class Factory
{
    /**
     * Make some event from api-request array
     *
     * @param  array $data api request data
     * @return Event
     */
    public static function makeFromApi(array $data)
    {
        if (isset($data['event'])) {
            switch ($data['event']) {
                case Type::CONVERSATION:
                    return new Conversation($data);
                case Type::MESSAGE:
                    return new Message($data);
                case Type::SEEN:
                    return new Seen($data);
                case Type::SUBSCRIBED:
                    return new Subscribed($data);
                case Type::UNSUBSCRIBED:
                    return new Unsubscribed($data);
                case Type::WEBHOOK:
                    return new Webhook($data);
            }
        }
        throw new ApiException('Unknow event data');
    }
}
