<?php

namespace Phpvcn\Sms\Drivers;

use Illuminate\Log\Writer;
use Phpvcn\Sms\OutgoingMessage;

class LogSMS implements DriverInterface
{

    /**
     * Laravel Logger.
     *
     * @var \GuzzleHttp\Client
     */
    protected $logger;

    /**
     * Create the CallFire instance.
     *
     * @param Illuminate\Log\Writer $logger
     */
    public function __construct(Writer $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Sends a SMS message.
     *
     * @param \Phpvcn\Sms\OutgoingMessage $message
     */
    public function send(OutgoingMessage $message)
    {
        $content = $message->composeMessage();
        foreach ($message->getTos() as $number) {
            $this->logger->notice("Sending SMS message to: $number  content: $content");
        }
    }
}
