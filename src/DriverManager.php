<?php

namespace Phpvcn\Sms;

use GuzzleHttp\Client;
use Illuminate\Support\Manager;
use Phpvcn\Sms\Drivers\LogSMS;
use Phpvcn\Sms\Drivers\LuosimaoSMS;
use Phpvcn\Sms\Drivers\MeilianSMS;

class DriverManager extends Manager
{
    /**
     * Get the default sms driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['services.sms_driver'];
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
        $config = $this->app['config']->get('services.meilian', []);

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
        $config = $this->app['config']->get('services.luosimao', []);

        $provider = new LuosimaoSMS(
            new Client,
            $config['api_key'],
            $config['sign']
        );

        return $provider;
    }
}
