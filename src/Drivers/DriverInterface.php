<?php

namespace Phpvcn\SMS\Drivers;

use Phpvcn\SMS\OutgoingMessage;

interface DriverInterface
{
    /**
     * Sends a SMS message.
     *
     * @param \Phpvcn\SMS\OutgoingMessage $message
     */
    public function send(OutgoingMessage $message);

}
