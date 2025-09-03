<?php

namespace NotificationChannels\ProdevelUK;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class AzureCommunicationServiceServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/azure-communication.php' => $this->app->configPath('azure-communication.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__.'/../config/azure-communication.php', 'azure-communication');
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->singleton(AzureCommunicationServiceClient::class, function () {
            return new AzureCommunicationServiceClient();
        });

        $this->app->singleton(AzureCommunicationServiceChannel::class);
    }
}
