<?php

namespace Samsara\Tests\Unit;

use Samsara\Samsara;
use Samsara\Tests\TestCase;
use Illuminate\Http\Client\Request;
use Samsara\SamsaraServiceProvider;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\ServiceProvider;
use PHPUnit\Framework\Attributes\DataProvider;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Contracts\Support\DeferrableProvider;

/**
 * Unit tests for the Samsara service provider.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class SamsaraServiceProviderTest extends TestCase
{
    #[Test]
    public function it_passes_config_to_samsara_instance(): void
    {
        $this->app['config']->set('samsara.timeout', 60);
        $this->app['config']->set('samsara.attempts', 5);
        $this->app['config']->set('samsara.retry_writes', true);
        $this->app['config']->set('samsara.retry_after_max_seconds', 45);
        $this->app['config']->set('samsara.per_page', 50);

        // Get a fresh instance
        $this->app->forgetInstance(Samsara::class);
        $samsara = $this->app->make(Samsara::class);

        $this->assertSame(60, $samsara->getConfig('timeout'));
        $this->assertSame(5, $samsara->getConfig('attempts'));
        $this->assertTrue($samsara->getConfig('retry_writes'));
        $this->assertSame(45, $samsara->getConfig('retry_after_max_seconds'));
        $this->assertSame(50, $samsara->getConfig('per_page'));
    }

    #[Test]
    public function it_registers_eagerly_without_deferred_service_metadata(): void
    {
        $provider = new SamsaraServiceProvider($this->app);

        $this->assertNotInstanceOf(DeferrableProvider::class, $provider);
        $this->assertSame(
            ServiceProvider::class,
            (new \ReflectionMethod($provider, 'provides'))->getDeclaringClass()->getName(),
        );
    }

    #[Test]
    public function it_registers_samsara_with_alias(): void
    {
        $samsara = $this->app->make('samsara');

        $this->assertInstanceOf(Samsara::class, $samsara);
    }

    #[Test]
    public function it_registers_the_config_publish_path_during_application_boot(): void
    {
        $paths = ServiceProvider::pathsToPublish(SamsaraServiceProvider::class, 'samsara-config');

        $this->assertCount(1, $paths);
        $this->assertSame(
            realpath(dirname(__DIR__, 2).'/config/samsara.php'),
            realpath((string) array_key_first($paths))
        );
        $this->assertSame(config_path('samsara.php'), current($paths));
    }

    #[Test]
    public function it_resets_the_scoped_client_between_application_lifecycles(): void
    {
        $first = $this->app->make(Samsara::class);

        $this->app->forgetScopedInstances();
        $second = $this->app->make(Samsara::class);

        $this->assertNotSame($first, $second);
    }

    #[Test]
    public function it_reuses_the_scoped_client_within_one_lifecycle(): void
    {
        $samsara1 = $this->app->make(Samsara::class);
        $samsara2 = $this->app->make(Samsara::class);

        $this->assertInstanceOf(Samsara::class, $samsara1);
        $this->assertSame($samsara1, $samsara2);
    }

    #[Test]
    #[DataProvider('configuredTokens')]
    public function it_sends_the_configured_api_key_as_bearer_token(?string $token, array $headers): void
    {
        $this->app['config']->set('samsara.api_key', $token);
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake(['*' => $http->response(['data' => []])]);
        $this->app->instance(HttpFactory::class, $http);
        $this->app->forgetInstance(Samsara::class);

        $this->app->make(Samsara::class)->drivers()->query()->get();

        $http->assertSentCount(1);
        $http->assertSent(fn (Request $request): bool => $request->header('Authorization') === $headers);
    }

    #[Test]
    public function it_uses_canada_endpoint_when_region_is_ca(): void
    {
        $this->app['config']->set('samsara.region', 'ca');
        $this->app['config']->set('samsara.attempts', 1);
        $this->app->forgetInstance(Samsara::class);

        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake(['*' => $http->response(['data' => []])]);
        $samsara = $this->app->make(Samsara::class);
        $samsara->setHttpFactory($http);
        $samsara->drivers()->query()->get();

        $this->assertSame('https://api.ca.samsara.com', $samsara->getBaseUrl());
        $http->assertSent(fn (Request $request): bool => str_starts_with(
            $request->url(),
            'https://api.ca.samsara.com/fleet/drivers'
        ));
    }

    #[Test]
    public function it_uses_configured_per_page_when_lazily_loading_without_an_explicit_limit(): void
    {
        $this->app['config']->set('samsara.per_page', 50);
        $this->app['config']->set('samsara.attempts', 1);
        $this->app->forgetInstance(Samsara::class);

        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake([
            '*' => $http->response([
                'data'       => [],
                'pagination' => ['endCursor' => null, 'hasNextPage' => false],
            ]),
        ]);

        $samsara = $this->app->make(Samsara::class);
        $samsara->setHttpFactory($http);
        $samsara->drivers()->query()->lazy()->all();

        $http->assertSent(fn (Request $request): bool => ($request->data()['limit'] ?? null) === 50);
    }

    #[Test]
    public function it_uses_configured_per_page_when_paginating_without_an_explicit_limit(): void
    {
        $this->app['config']->set('samsara.per_page', 50);
        $this->app['config']->set('samsara.attempts', 1);
        $this->app->forgetInstance(Samsara::class);

        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake([
            '*' => $http->response([
                'data'       => [],
                'pagination' => ['endCursor' => null, 'hasNextPage' => false],
            ]),
        ]);

        $samsara = $this->app->make(Samsara::class);
        $samsara->setHttpFactory($http);
        $samsara->drivers()->query()->paginate();

        $http->assertSent(fn (Request $request): bool => ($request->data()['limit'] ?? null) === 50);
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
    public function it_uses_the_laravel_http_factory_for_the_registered_client(): void
    {
        $http = $this->app->make(HttpFactory::class);
        $http->preventStrayRequests();
        $http->fake(['*' => $http->response(['data' => []])]);

        $this->app->forgetInstance(Samsara::class);
        $samsara = $this->app->make(Samsara::class);

        $this->assertCount(0, $samsara->drivers()->query()->get());
        $http->assertSentCount(1);
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

    /** @return array<string, array{string|null, list<string>}> */
    public static function configuredTokens(): array
    {
        return [
            'configured' => ['my-test-token', ['Bearer my-test-token']],
            'absent'     => [null, []],
        ];
    }
}
