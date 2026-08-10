<?php

namespace Samsara\Tests\Unit;

use Samsara\Samsara;
use Samsara\Tests\TestCase;
use Samsara\SamsaraServiceProvider;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\Factory as HttpFactory;

/**
 * Unit tests for the Samsara service provider.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class SamsaraServiceProviderTest extends TestCase
{
    #[Test]
    public function it_configures_api_key_from_config(): void
    {
        $this->app['config']->set('samsara.api_key', 'my-test-token');

        // Get a fresh instance
        $this->app->forgetInstance(Samsara::class);
        $samsara = $this->app->make(Samsara::class);

        $this->assertTrue($samsara->hasToken());
    }

    #[Test]
    public function it_passes_config_to_samsara_instance(): void
    {
        $this->app['config']->set('samsara.timeout', 60);
        $this->app['config']->set('samsara.retry', 5);
        $this->app['config']->set('samsara.per_page', 50);

        // Get a fresh instance
        $this->app->forgetInstance(Samsara::class);
        $samsara = $this->app->make(Samsara::class);

        $this->assertSame(60, $samsara->getConfig('timeout'));
        $this->assertSame(5, $samsara->getConfig('retry'));
        $this->assertSame(50, $samsara->getConfig('per_page'));
    }

    #[Test]
    public function it_provides_samsara_service(): void
    {
        $provider = new SamsaraServiceProvider($this->app);

        $this->assertContains(Samsara::class, $provider->provides());
        $this->assertContains('samsara', $provider->provides());
    }

    #[Test]
    public function it_registers_samsara_singleton(): void
    {
        $samsara1 = $this->app->make(Samsara::class);
        $samsara2 = $this->app->make(Samsara::class);

        $this->assertInstanceOf(Samsara::class, $samsara1);
        $this->assertSame($samsara1, $samsara2);
    }

    #[Test]
    public function it_registers_samsara_with_alias(): void
    {
        $samsara = $this->app->make('samsara');

        $this->assertInstanceOf(Samsara::class, $samsara);
    }

    #[Test]
    public function it_uses_canada_endpoint_when_region_is_ca(): void
    {
        $this->app['config']->set('samsara.region', 'ca');
        $this->app['config']->set('samsara.retry', 0);
        $this->app->forgetInstance(Samsara::class);

        $http = new HttpFactory;
        $http->fake(['*' => $http->response(['data' => []])]);
        $samsara = $this->app->make(Samsara::class);
        $samsara->setHttpFactory($http);
        $samsara->drivers()->query()->get();

        $this->assertSame('https://api.ca.samsara.com', $samsara->getBaseUrl());
        $http->assertSent(fn ($request) => str_starts_with(
            $request->url(),
            'https://api.ca.samsara.com/fleet/drivers'
        ));
    }

    #[Test]
    public function it_uses_configured_per_page_when_lazily_loading_without_an_explicit_limit(): void
    {
        $this->app['config']->set('samsara.per_page', 50);
        $this->app['config']->set('samsara.retry', 0);
        $this->app->forgetInstance(Samsara::class);

        $http = new HttpFactory;
        $http->fake([
            '*' => $http->response([
                'data'       => [],
                'pagination' => ['endCursor' => null, 'hasNextPage' => false],
            ]),
        ]);

        $samsara = $this->app->make(Samsara::class);
        $samsara->setHttpFactory($http);
        $samsara->drivers()->query()->lazy()->all();

        $http->assertSent(fn ($request) => ($request->data()['limit'] ?? null) === 50);
    }

    #[Test]
    public function it_uses_configured_per_page_when_paginating_without_an_explicit_limit(): void
    {
        $this->app['config']->set('samsara.per_page', 50);
        $this->app['config']->set('samsara.retry', 0);
        $this->app->forgetInstance(Samsara::class);

        $http = new HttpFactory;
        $http->fake([
            '*' => $http->response([
                'data'       => [],
                'pagination' => ['endCursor' => null, 'hasNextPage' => false],
            ]),
        ]);

        $samsara = $this->app->make(Samsara::class);
        $samsara->setHttpFactory($http);
        $samsara->drivers()->query()->paginate();

        $http->assertSent(fn ($request) => ($request->data()['limit'] ?? null) === 50);
    }

    #[Test]
    public function it_uses_eu_endpoint_when_region_is_eu(): void
    {
        $this->app['config']->set('samsara.region', 'eu');

        // Get a fresh instance
        $this->app->forgetInstance(Samsara::class);
        $samsara = $this->app->make(Samsara::class);

        $this->assertSame('https://api.eu.samsara.com', $samsara->getBaseUrl());
    }

    #[Test]
    public function it_uses_us_endpoint_when_region_is_us(): void
    {
        $this->app['config']->set('samsara.region', 'us');

        // Get a fresh instance
        $this->app->forgetInstance(Samsara::class);
        $samsara = $this->app->make(Samsara::class);

        $this->assertSame('https://api.samsara.com', $samsara->getBaseUrl());
    }
}
