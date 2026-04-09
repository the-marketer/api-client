<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\Laravel;

use Illuminate\Mail\MailManager;
use Illuminate\Support\ServiceProvider;
use TheMarketer\ApiClient\Client;
use TheMarketer\ApiClient\Laravel\Mail\TheMarketerTransport;

class ApiClientServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/themarketer-api-client.php', 'themarketer-api-client');

        $this->app->singleton(Client::class, static function ($app): Client {
            /** @var array{
             *     customerId: string,
             *     restKey: string,
             *     trackingKey?: string,
             *     restUrl?: string,
             *     trackingUrl?: string,
             *     maxRetryAttempts?: int
             * } $config
             */
            $config = (array)$app['config']->get('themarketer-api-client', []);

            return new Client($config);
        });

        $this->app->alias(Client::class, 'themarketer.api-client');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/themarketer-api-client.php' => $this->app->configPath('themarketer-api-client.php'),
        ], 'themarketer-api-client-config');

        $this->app->afterResolving('mail.manager', function ($manager): void {
            if (!$manager instanceof MailManager) {
                return;
            }

            $manager->extend('themarketer', function (array $config = []): TheMarketerTransport {
                return new TheMarketerTransport($this->app->make(Client::class), $config);
            });
        });
    }
}
