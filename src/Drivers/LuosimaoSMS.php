<?php

namespace Phpvcn\SMS\Drivers;

use GuzzleHttp\Client;
use Phpvcn\SMS\MakesRequests;
use Phpvcn\SMS\OutgoingMessage;

class LuosimaoSMS implements DriverInterface
{
    use MakesRequests;
    
    /**
     * The API's URL.
     *
     * @var string
     */
    protected $apiBase = 'http://sms-api.luosimao.com/v1';

    protected $sign = '【快乐芒果】'; //签名
    protected $apikey = '';

    /**
     * The Guzzle HTTP Client.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * LuosimaoSMS constructor.
     *
     * @param Client $client
     * @param $accountKey
     * @param $passCode
     * @param string $callbackOption
     */
    public function __construct(Client $client, $api_key)
    {
        $this->client = $client;
        $this->apikey = $api_key;
    }

    /**
     * Sends a SMS message.
     *
     * @param \Phpvcn\SMS\OutgoingMessage $message
     */
    public function send(OutgoingMessage $message)
    {
        $composeMessage = $message->composeMessage() . $this->sign;

        $numbers = $message->getTos();

        if (count($numbers) > 1) {
            $endpoint = '/send_batch.json';
            $data = [
                'mobile_list' => $numbers = implode(',', $numbers),
                'message' => $composeMessage,
            ];
        } else {
            $endpoint = '/send.json';
            $data = [
                'mobile' => $numbers[0],
                'message' => $composeMessage,
            ];
        }

        $this->buildCall($endpoint);
        $this->buildBody($data);

        $response = $this->postRequest();
        $responseBody = $response->getBody()->getContents();
        return $responseBody;
    }

    /**
     * Creates and sends a POST request to the requested URL.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    protected function postRequest()
    {
        $response = $this->client->post($this->buildUrl(),
            [
                'form_params' => $this->getBody(),
                'curl' => [
                    CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
                    CURLOPT_USERPWD => $this->apikey
                ]
            ]);
        if ($response->getStatusCode() != 201 && $response->getStatusCode() != 200) {
            throw new \Exception('Unable to request from API. HTTP Error: '.$response->getStatusCode());
        }

        return $response;
    }
}
