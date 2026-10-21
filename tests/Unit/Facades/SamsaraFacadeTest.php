<?php

namespace Samsara\Tests\Unit\Facades;

use Samsara\Samsara;
use Samsara\Tests\TestCase;
use Samsara\Testing\SamsaraFake;
use Illuminate\Http\Client\Request;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\PendingRequest;
use Samsara\Facades\Samsara as SamsaraFacade;
use Illuminate\Http\Client\Factory as HttpFactory;

/**
 * Unit tests for the Samsara facade.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class SamsaraFacadeTest extends TestCase
{
    #[Test]
    public function it_can_call_client_method(): void
    {
        $this->app->forgetInstance(Samsara::class);

        $client = SamsaraFacade::client();

        $this->assertInstanceOf(PendingRequest::class, $client);
    }

    #[Test]
    public function it_can_call_get_base_url_method(): void
    {
        $this->app->forgetInstance(Samsara::class);

        $this->assertSame('https://api.samsara.com', SamsaraFacade::getBaseUrl());
    }

    #[Test]
    public function it_can_call_has_token_method(): void
    {
        $this->app['config']->set('samsara.api_key', 'test-token');
        $this->app->forgetInstance(Samsara::class);

        $this->assertTrue(SamsaraFacade::hasToken());
    }

    #[Test]
    public function it_can_create_fake_instance(): void
    {
        $fake = SamsaraFacade::fake();

        $this->assertInstanceOf(SamsaraFake::class, $fake);
    }

    #[Test]
    public function it_does_not_leak_facade_region_into_later_token_clones(): void
    {
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake(['*' => $http->response(['data' => []])]);
        $root = SamsaraFacade::getFacadeRoot();
        $root->setHttpFactory($http);
        $europe = SamsaraFacade::withRegion('eu');
        $carrier = SamsaraFacade::withToken('carrier-token');

        $europe->drivers()->query()->get();
        $carrier->drivers()->query()->get();

        $this->assertNotSame($root, $europe);
        $this->assertSame('https://api.samsara.com', $root->getBaseUrl());
        $http->assertSentInOrder([
            fn (Request $request): bool => parse_url($request->url(), PHP_URL_HOST) === 'api.eu.samsara.com'
                && $request->header('Authorization') === ['Bearer test-api-key'],
            fn (Request $request): bool => parse_url($request->url(), PHP_URL_HOST) === 'api.samsara.com'
                && $request->header('Authorization') === ['Bearer carrier-token'],
        ]);
    }

    #[Test]
    public function it_fake_replaces_deferred_facade_class_and_alias_bindings(): void
    {
        $originalFacadeApplication = \Illuminate\Support\Facades\Facade::getFacadeApplication();
        $originalContainer = \Illuminate\Container\Container::getInstance();

        try {
            $app = new \Illuminate\Foundation\Application(dirname(__DIR__, 3));
            $app->instance('config', new \Illuminate\Config\Repository([
                'samsara' => [
                    'api_key'  => 'test-token',
                    'region'   => 'us',
                    'timeout'  => 30,
                    'attempts' => 1,
                    'per_page' => 100,
                ],
            ]));
            $app->setDeferredServices([
                Samsara::class => \Samsara\SamsaraServiceProvider::class,
                'samsara'      => \Samsara\SamsaraServiceProvider::class,
            ]);

            \Illuminate\Support\Facades\Facade::clearResolvedInstances();
            \Illuminate\Support\Facades\Facade::setFacadeApplication($app);

            $fake = SamsaraFacade::fake();
            $alias = $app->make('samsara');

            $this->assertSame($fake, SamsaraFacade::getFacadeRoot());
            $this->assertSame($fake, $app->make(Samsara::class));
            $this->assertSame($fake, $alias);
        } finally {
            \Illuminate\Support\Facades\Facade::clearResolvedInstances();
            \Illuminate\Support\Facades\Facade::setFacadeApplication($originalFacadeApplication);
            \Illuminate\Container\Container::setInstance($originalContainer);
        }
    }

    #[Test]
    public function it_fake_replaces_resolved_facade_and_container_singleton(): void
    {
        SamsaraFacade::getFacadeRoot();

        $fake = SamsaraFacade::fake();

        $this->assertSame($fake, SamsaraFacade::getFacadeRoot());
        $this->assertSame($fake, $this->app->make(Samsara::class));
    }

    #[Test]
    public function it_fakes_a_two_page_cursor_flow(): void
    {
        $fake = SamsaraFacade::fake([
            'fleet/drivers' => [
                'data'       => [['id' => 'driver-1']],
                'pagination' => ['endCursor' => 'page-2', 'hasNextPage' => true],
            ],
        ]);

        $firstPage = SamsaraFacade::drivers()->query()->paginate();

        $this->assertSame(['driver-1'], $firstPage->items()->pluck('id')->all());
        $this->assertTrue($firstPage->hasMorePages());

        $fake->fakeResponse('fleet/drivers', [
            'data'       => [['id' => 'driver-2']],
            'pagination' => ['endCursor' => null, 'hasNextPage' => false],
        ]);
        $secondPage = $firstPage->nextPage();

        $this->assertNotNull($secondPage);
        $this->assertSame(['driver-2'], $secondPage->items()->pluck('id')->all());
        $this->assertFalse($secondPage->hasMorePages());
        $this->assertNull($secondPage->nextPage());
        $fake->getHttpFactory()->assertSentInOrder([
            fn (Request $request): bool => ! array_key_exists('after', $request->data()),
            fn (Request $request): bool => $request['after'] === 'page-2',
        ]);
        $fake->assertSentCount(2);
    }

    #[Test]
    public function it_resolves_samsara_instance(): void
    {
        $samsara = SamsaraFacade::getFacadeRoot();

        $this->assertInstanceOf(Samsara::class, $samsara);
    }

    #[Test]
    public function it_returns_an_isolated_token_clone_through_the_facade(): void
    {
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake(['*' => $http->response(['data' => []])]);
        $root = SamsaraFacade::getFacadeRoot();
        $root->setHttpFactory($http);
        $clone = SamsaraFacade::withToken('new-token');

        $clone->drivers()->query()->get();
        SamsaraFacade::drivers()->query()->get();

        $this->assertNotSame($root, $clone);
        $http->assertSentInOrder([
            fn (Request $request): bool => $request->header('Authorization') === ['Bearer new-token'],
            fn (Request $request): bool => $request->header('Authorization') === ['Bearer test-api-key'],
        ]);
    }

    #[Test]
    public function it_stages_responses_passed_to_fake(): void
    {
        $fake = SamsaraFacade::fake(['fleet/drivers' => [['id' => 'driver-1']]]);

        $this->assertSame('driver-1', SamsaraFacade::drivers()->query()->first()->id);
        $fake->assertRequested('/fleet/drivers');
    }
}
