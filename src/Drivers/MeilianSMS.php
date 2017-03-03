<?php
namespace Phpvcn\Sms\Drivers;

use GuzzleHttp\Client;
use Phpvcn\Sms\MakesRequests;
use Phpvcn\Sms\OutgoingMessage;

class MeilianSMS implements DriverInterface
{
    use MakesRequests;

    /**
     * The API's URL.
     *
     * @var string
     */
    protected $apiBase = 'http://m.5c.com.cn/api/send/index.php';

    /**
     * The Guzzle HTTP Client.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    protected $apikey;

    /**
     * Create the MeilianSMS instance.
     *
     * @param Client $client The Guzzle Client
     */
    public function __construct(Client $client, $username, $password, $api_key)
    {
        $this->client = $client;
        $this->setUser($username);
        $this->setPassword($password);
        $this->apikey = $api_key;
    }

    /**
     * Sends a SMS message.
     *
     * @param \Phpvcn\Sms\OutgoingMessage $message
     */
    public function send(OutgoingMessage $message)
    {
        $composeMessage = $message->composeMessage();

        $numbers = implode(',', $message->getTos());

        $data = [
            'username' => $this->auth['username'],
            'password_md5' => md5($this->auth['password']),
            'apikey' => $this->apikey,
            'mobile' => $numbers,
            'content' => urlencode($composeMessage),
            'encode' => 'UTF-8',
        ];

        $this->buildCall('');
        $this->buildBody($data);

        $response = $this->postRequest();
        if ($response->getStatusCode() != 201 && $response->getStatusCode() != 200) {
            throw new \Exception('Unable to request from API. HTTP Error: '.$response->getStatusCode());
        }
        $responseBody = $response->getBody()->getContents();
        return $responseBody;
    }

    /**
     * Checks if the transaction has an error
     *
     * @param $body
     * @return bool
     */
    protected function hasError($body)
    {
        if ($body != '100') {
            return $body;
        }
        return false;
    }
}
