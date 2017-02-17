<?php

namespace Phpvcn\SMS;

use GuzzleHttp\Client;
use Illuminate\Support\Manager;
use Phpvcn\SMS\Drivers\LogSMS;
use Phpvcn\SMS\Drivers\LuosimaoSMS;
use Phpvcn\SMS\Drivers\MeilianSMS;

class DriverManager extends Manager
{
    /**
     * Get the default sms driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['sms.driver'];
    }

    /**
     * Set the default sms driver name.
     *
     * @param string $name
     */
    public function setDefaultDriver($name)
    {
        $this->app['config']['sms.driver'] = $name;
    }

    /**
     * Create an instance of the Log driver.
     *
     * @return LogSMS
     */
    protected function createLogDriver()
    {
        $provider = new LogSMS($this->app['log']);

        return $provider;
    }

    /**
     * Create an instance of the Meilian driver
     *
     * @return Meilian
     */
    protected function createMeilianDriver()
    {
        $config = $this->app['config']->get('sms.meilian', []);

        $provider = new MeilianSMS(
            new Client,
            $config['api_user'],
            $config['api_pass'],
            $config['api_key']
        );

        return $provider;
    }

    /**
     * Create an instance of the Luosimao driver
     *
     * @return Meilian
     */
    protected function createLuosimaoDriver()
    {
        $config = $this->app['config']->get('sms.luosimao', []);

        $provider = new LuosimaoSMS(
            new Client,
            $config['api_key'],
            $config['sign']
        );

        return $provider;
    }
}
