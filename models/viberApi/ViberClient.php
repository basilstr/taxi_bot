<?php

namespace app\models\viberApi;

use app\models\viberApi\Exception\ApiException;
use app\models\viberApi\Api\Response;
use app\models\viberApi\Api\Message;
use app\models\viberApi\Api\Event\Type;

/**
 * Simple rest client for Viber public account (PA)
 *
 * @see https://developers.viber.com/api/rest-bot-api/index.html
 *
 * @author Novikov Bogdan <hcbogdan@gmail.com>
 */
class ViberClient
{
    /**
     * Api endpoint base
     *
     * @var string
     */
    const BASE_URI = 'https://chatapi.viber.com/pa/';

    /**
     * Access token
     *
     * @var string
     */
    protected $token;

    /**
     * Http network client
     *
     * @var \GuzzleHttp\Client
     */
    protected $http;

    /**
     * Create api client. Options:
     * token  required  string  authentication token
     * http   optional  array   adapter parameters
     *
     * @param $token
     */


    public function __construct($token)
    {
        if (!isset($token)) {
            throw new ApiException('No token provided');
        }
        $this->token = $token;
        $httpInit = [
            'base_uri' => self::BASE_URI,
        ];
        $this->http = new \GuzzleHttp\Client($httpInit);
    }

    /**
     * Get access token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    public function call($method, $data)
    {
        try {
            $response = $this->http->request('POST', $method, [
                'headers' => [
                    'X-Viber-Auth-Token' => $this->token
                ],
                'json' => $data
            ]);
            return Response::create($response);
        } catch (\RuntimeException $e) {
            $item = new Response();
            $receiver = isset($data['receiver']) ? $data['receiver'] : "";
            $item->addError('receiver', $receiver);
            $item->addError('code', $e->getCode());
            $item->addError('message', $e->getMessage());
            return $item;
        }
    }

    /**
     * Set webhook url.
     *
     * For security reasons only URLs with valid and * official SSL certificate
     *
     * from a trusted CA will be allowed.
     * @param $url
     * @param null $eventTypes
     * @return Response
     */
    public function setWebhook($url)
    {
        $eventTypes = [Type::SUBSCRIBED, Type::CONVERSATION, Type::MESSAGE, Type::SEEN];
        if (empty($url) || !preg_match('|^https://.*|s', $url)) {
            throw new ApiException('Invalid webhook url: ' . $url);
        }

        return $this->call('set_webhook', [
            'url' => $url,
            'event_types' => $eventTypes,
        ]);
    }


    /**
     * Delete webhook url.
     * @return Response
     */
    public function deleteWebhook()
    {
        return $this->call('set_webhook', [
            'url' => '',
        ]);
    }

    /**
     * Fetch the public account’s details as registered in Viber
     *
     * @return Response
     */
    public function getAccountInfo()
    {
        return $this->call('get_account_info', [1 => 1]);
    }

    /**
     * Fetch the details of a specific Viber user based on his unique user ID.
     *
     * The user ID can be obtained from the callbacks sent to the PA regrading
     * user's actions. This request can be sent twice during a 12 hours period
     * for each user ID.
     *
     * @param $userId
     * @return  Response
     */
    public function getUserDetails($chatId)
    {
        return $this->call('get_user_details', [
            'id' => $chatId
        ]);
    }

    /**
     * Fetch the online status of a given subscribed PA members.
     *
     * The API supports up to 100 user id per request and those users must be
     * subscribed to the PA.
     * 0 - for online,
     * 1 - for offline,
     * 2 - for undisclosed - user set Viber to hide status,
     * 3 - for try later - internal error,
     * 4 - for unavailable - not a Viber user / unsubscribed / unregistered
     *
     * @param  array $userIds list of user ids
     * @return Response
     */
    public function getOnlineStatus(array $userIds)
    {
        return $this->call('get_online', [
            'ids' => $userIds
        ]);
    }

    /**
     * Send messages to Viber users who subscribe to the PA.
     *
     * @param  Message $message
     * @return Response
     */
    public function sendMessage(Message $message)
    {
        return $this->call('send_message', $message->toApiArray());
    }
}
