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
        $this->app->singleton(AzureCommunicationServiceClient::class, function ($app) {
            $config = $app['config'];
            $connectionString = is_array($config) 
                ? $config['azure-communication']['connection_string'] ?? null
                : $config->get('azure-communication.connection_string');
            return new AzureCommunicationServiceClient($connectionString);
        });

        $this->app->singleton(AzureCommunicationServiceChannel::class);
    }
}
