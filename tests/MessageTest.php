<?php

use Mockery as m;
use Phpvcn\SMS\OutgoingMessage;

class MessageTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function setUp()
    {
        $this->message = new OutgoingMessage(m::mock('\Illuminate\View\Factory'));
    }

    public function testAddTo()
    {
        $to = ['+15555555555'];
        $this->message->to('+15555555555');

        $this->assertEquals($to, $this->message->getTo());
    }
}
