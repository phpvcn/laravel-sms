<?php

namespace Phpvcn\SMS\Events;

class SmsSending
{
    /**
     * The OutgoingMessage instance.
     *
     * @var \Phpvcn\SMS\OutgoingMessage
     */
    public $message;

    /**
     * The Http Request Response.
     *
     * @var mix
     */
    public $response;

    /**
     * Create a new event instance.
     *
     * @param  \Phpvcn\SMS\OutgoingMessage  $message
     * @return void
     */
    public function __construct($message, $response)
    {
        $this->message = $message;
        $this->response = $response;
    }
}
