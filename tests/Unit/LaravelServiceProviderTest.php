<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Mail\MailManager;
use Tests\TestCase;
use TheMarketer\ApiClient\Client;
use TheMarketer\ApiClient\Laravel\Facades\TheMarketer;
use TheMarketer\ApiClient\Laravel\ApiClientServiceProvider;
use TheMarketer\ApiClient\Laravel\Mail\TheMarketerTransport;

final class LaravelServiceProviderTest extends TestCase
{
    /**
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [ApiClientServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('themarketer-api-client', [
            'customerId' => 'laravel-customer',
            'restKey' => 'laravel-rest-key',
            'trackingKey' => 'laravel-tracking-key',
            'restUrl' => 'https://rest.example.test',
            'trackingUrl' => 'https://tracking.example.test',
            'maxRetryAttempts' => 4,
        ]);

        $app['config']->set('mail.mailers.themarketer', [
            'transport' => 'themarketer',
        ]);
    }

    /**
     * @throws BindingResolutionException
     */
    public function testClientIsResolvedFromContainerUsingPackageConfig(): void
    {
        $client = $this->app->make(Client::class);

        $this->assertInstanceOf(Client::class, $client);
        $this->assertSame('laravel-customer', $client->config()->customerId());
        $this->assertSame('laravel-rest-key', $client->config()->restKey());
        $this->assertSame('laravel-tracking-key', $client->config()->trackingKey());
        $this->assertSame('https://rest.example.test', $client->config()->restUrl());
        $this->assertSame('https://tracking.example.test', $client->config()->trackingUrl());
    }

    public function testFacadeResolvesClientSingleton(): void
    {
        $this->assertInstanceOf(Client::class, TheMarketer::getFacadeRoot());
        $this->assertSame('laravel-customer', TheMarketer::config()->customerId());
    }

    public function testRegistersTheMarketerMailerTransport(): void
    {
        $mailManager = $this->app->make('mail.manager');
        $this->assertInstanceOf(MailManager::class, $mailManager);

        $transport = $mailManager->mailer('themarketer')->getSymfonyTransport();
        $this->assertInstanceOf(TheMarketerTransport::class, $transport);
    }
}
