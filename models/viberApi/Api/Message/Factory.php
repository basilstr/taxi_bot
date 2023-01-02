<?php

namespace app\models\viberApi\Api\Message;

use app\models\viberApi\Exception\ApiException;
use app\models\viberApi\Api\Message;

/**
 * Message factory
 *
 * @author Novikov Bogdan <hcbogdan@gmail.com>
 */
class Factory
{
    /**
     * Make certain message from api-request array
     *
     * @param  array $data api request data
     * @return Message
     */
    public static function makeFromApi(array $data)
    {
        if (isset($data['type'])) {
            switch ($data['type']) {
                case Type::TEXT:
                    return new Text($data);
                case Type::CONTACT:
                    return new Contact($data);
                case Type::PICTURE:
                    return new Picture($data);
                case Type::LOCATION:
                    return new Location($data);
                case Type::FILE:
                    return new File($data);
                case Type::VIDEO:
                    return new Video($data);
            }
        }
        throw new ApiException('Unknow message data');
    }
}
