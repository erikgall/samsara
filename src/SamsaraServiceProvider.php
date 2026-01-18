<?php

namespace ErikGall\Samsara;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

/**
 * Samsara service provider for Laravel.
 *
 * Registers the Samsara client as a singleton and publishes configuration.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class SamsaraServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/samsara.php' => config_path('samsara.php'),
            ], 'samsara-config');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [
            Samsara::class,
            'samsara',
        ];
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/samsara.php',
            'samsara'
        );

        $this->app->singleton(Samsara::class, function ($app) {
            $config = $app['config']['samsara'];

            $samsara = new Samsara(
                $config['api_key'] ?? null,
                [
                    'timeout'  => $config['timeout'] ?? 30,
                    'retry'    => $config['retry'] ?? 3,
                    'per_page' => $config['per_page'] ?? 100,
                ]
            );

            if (($config['region'] ?? 'us') === 'eu') {
                $samsara->useEuEndpoint();
            }

            return $samsara;
        });

        $this->app->alias(Samsara::class, 'samsara');
    }
}
