<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient;

use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Facade;
use Illuminate\Translation\TranslationServiceProvider;
use Illuminate\Validation\ValidationServiceProvider;
use Spatie\LaravelData\LaravelDataServiceProvider;

/**
 * Spatie Laravel Data relies on Laravel's config(), app(), Validator facade, etc.
 * Call this once before using the API client outside a Laravel application.
 */
function bootstrap_laravel_data_if_needed(): void
{
    static $bootstrapped = false;
    if ($bootstrapped) {
        return;
    }

    if (function_exists('app') && app()->bound('config')) {
        $bootstrapped = true;

        return;
    }

    $basePath = dirname(__DIR__);

    $app = new Application($basePath);

    $app->singleton('config', function () use ($basePath) {
        return new Repository([
            'app' => [
                'locale' => 'en',
                'fallback_locale' => 'en',
            ],
            'data' => require __DIR__.'/laravel_data_config.php',
        ]);
    });

    $app->singleton('files', static fn () => new \Illuminate\Filesystem\Filesystem());

    $app->register(TranslationServiceProvider::class);
    $app->register(ValidationServiceProvider::class);
    $app->register(LaravelDataServiceProvider::class);

    Facade::setFacadeApplication($app);

    $app->boot();

    $bootstrapped = true;
}
