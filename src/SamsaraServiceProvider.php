<?php

namespace Samsara;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Samsara\Webhooks\VerifyWebhookSignature;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Client\Factory as HttpFactory;

/**
 * Samsara service provider for Laravel.
 *
 * Registers the Samsara client as a scoped service and publishes configuration.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class SamsaraServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->make(Router::class)->aliasMiddleware(
            'samsara.webhook',
            VerifyWebhookSignature::class,
        );

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/samsara.php' => config_path('samsara.php'),
            ], 'samsara-config');
        }
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

        $this->app->scoped(Samsara::class, function (Application $app): Samsara {
            $config = $app->make('config')->get('samsara');

            return new Samsara(
                $config['api_key'] ?? null,
                [
                    'timeout'                 => $config['timeout'] ?? 30,
                    'attempts'                => $config['attempts'] ?? 3,
                    'retry_writes'            => $config['retry_writes'] ?? false,
                    'retry_after_max_seconds' => $config['retry_after_max_seconds'] ?? 30,
                    'per_page'                => $config['per_page'] ?? 100,
                    'region'                  => $config['region'] ?? 'us',
                ],
                $app->make(HttpFactory::class),
            );
        });

        $this->app->alias(Samsara::class, 'samsara');
    }
}
