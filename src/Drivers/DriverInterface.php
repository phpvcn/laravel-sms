<?php

namespace Phpvcn\Sms\Drivers;

use Phpvcn\Sms\OutgoingMessage;

interface DriverInterface
{
    /**
     * Sends a SMS message.
     *
     * @param \Phpvcn\Sms\OutgoingMessage $message
     */
    public function send(OutgoingMessage $message);

}
