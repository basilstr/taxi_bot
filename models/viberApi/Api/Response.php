<?php

namespace app\models\viberApi\Api;

use app\models\viberApi\Exception\ApiException;

/**
 * Manage backend response, translate api error ot exception
 *
 * @author Novikov Bogdan <hcbogdan@gmail.com>
 */
class Response
{
    /**
     * Raw response data
     *
     * @var array
     */
    protected $data;
    protected $error = [];

    /**
     * @param \GuzzleHttp\Psr7\Response $response
     * @return Response
     */
    public static function create(\GuzzleHttp\Psr7\Response $response)
    {
        // - validate body
        $data = json_decode($response->getBody(), true, 512, JSON_BIGINT_AS_STRING);
        if (empty($data)) {
            throw new ApiException('Invalid response body');
        }
        // - validate internal data
        if (isset($data['status'])) {
            if ($data['status'] != 0) {
                throw new ApiException('Remote error: ' .
                    (isset($data['status_message']) ? $data['status_message'] : '-'),
                    $data['status']);
            }
            $item = new self();
            $item->data = $data;
            return $item;
        }
        throw new ApiException('Invalid response json');
    }

    /**
     * Get the value of Raw response data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    public function isError()
    {
        return count($this->error) > 0;
    }

    public function getError()
    {
        return $this->error;
    }

    public function addError($key, $value)
    {
        return $this->error[$key] = $value;
    }

    public function getMessageToken()
    {
        return isset($this->data['message_token']) ? $this->data['message_token'] : '';
    }
}
