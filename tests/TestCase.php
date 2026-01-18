<?php

namespace ErikGall\Samsara\Tests;

use ErikGall\Samsara\SamsaraServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

/**
 * Base TestCase for Samsara SDK tests.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function defineEnvironment($app): void
    {
        $app['config']->set('samsara.api_key', 'test-api-key');
        $app['config']->set('samsara.region', 'us');
        $app['config']->set('samsara.timeout', 30);
        $app['config']->set('samsara.retry', 3);
        $app['config']->set('samsara.per_page', 100);
    }

    /**
     * Get package aliases.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<string, class-string>
     */
    protected function getPackageAliases($app): array
    {
        return [
            'Samsara' => \ErikGall\Samsara\Facades\Samsara::class,
        ];
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            SamsaraServiceProvider::class,
        ];
    }
}
