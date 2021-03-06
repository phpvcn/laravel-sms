<?php

namespace Phpvcn\Sms;

use Illuminate\Support\ServiceProvider;

class SmsServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        //
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/services.php', 'services'
        );

        $this->app->singleton('sms', function ($app) {
            $this->registerSender();
            $sms = new Sms($app['sms.sender'], $app['view'], $app['events']);
            $this->setSMSDependencies($sms, $app);

            return $sms;
        });
    }

    /**
     * Register the correct driver based on the config file.
     */
    public function registerSender()
    {
        $this->app->singleton('sms.sender', function ($app) {
            return (new DriverManager($app))->driver();
        });
    }

    /**
     * Set a few dependencies on the sms instance.
     *
     * @param SMS $sms
     * @param  $app
     */
    private function setSMSDependencies($sms, $app)
    {
        $sms->setContainer($app);
        if ($app->bound('queue')) {
            $sms->setQueue($app['queue']);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['sms', 'sms.sender'];
    }
}
