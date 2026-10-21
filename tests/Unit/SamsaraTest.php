<?php

namespace Samsara\Tests\Unit;

use Samsara\Samsara;
use Samsara\Tests\TestCase;
use Illuminate\Support\Sleep;
use Illuminate\Support\Carbon;
use Illuminate\Http\Client\Request;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Exceptions\ServerException;
use Illuminate\Http\Client\PendingRequest;
use Samsara\Exceptions\RateLimitException;
use Samsara\Data\Organization\Organization;
use Illuminate\Http\Client\RequestException;
use Samsara\Resources\Fleet\DriversResource;
use Samsara\Resources\Fleet\TrailersResource;
use Samsara\Resources\Fleet\VehiclesResource;
use PHPUnit\Framework\Attributes\DataProvider;
use Samsara\Exceptions\AuthorizationException;
use Samsara\Resources\Dispatch\RoutesResource;
use Samsara\Resources\Fleet\EquipmentResource;
use Illuminate\Http\Client\ConnectionException;
use Samsara\Resources\Telematics\TripsResource;
use Samsara\Resources\Industrial\AssetsResource;
use Samsara\Resources\Organization\TagsResource;
use Samsara\Resources\Dispatch\AddressesResource;
use Samsara\Resources\Industrial\SensorsResource;
use Samsara\Resources\Organization\UsersResource;
use Samsara\Resources\Safety\MaintenanceResource;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Resources\Safety\SafetyEventsResource;
use Samsara\Resources\Industrial\IndustrialResource;
use Samsara\Resources\Integrations\GatewaysResource;
use Samsara\Resources\Integrations\WebhooksResource;
use Samsara\Resources\Organization\ContactsResource;
use Samsara\Resources\Safety\HoursOfServiceResource;
use Samsara\Resources\Telematics\VehicleStatsResource;
use Samsara\Resources\Telematics\VehicleLocationsResource;
use Samsara\Resources\Integrations\LiveSharingLinksResource;

/**
 * Unit tests for the Samsara main client class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class SamsaraTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2030-01-01 00:00:00', 'UTC'));
        Sleep::fake();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        Sleep::fake(false);

        parent::tearDown();
    }

    #[Test]
    public function it_accepts_an_http_factory_in_the_constructor(): void
    {
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake(['*' => $http->response(['data' => []])]);

        $samsara = new Samsara('test-token', ['attempts' => 1], $http);
        $drivers = $samsara->drivers()->query()->get();

        $this->assertCount(0, $drivers);
        $http->assertSentCount(1);
    }

    #[Test]
    public function it_accepts_config_array_in_constructor(): void
    {
        $config = [
            'timeout'  => 60,
            'attempts' => 5,
        ];

        $samsara = new Samsara('test-token', $config);

        $this->assertInstanceOf(Samsara::class, $samsara);
    }

    #[Test]
    #[DataProvider('timeouts')]
    public function it_applies_the_configured_timeout_to_outbound_requests(array $config, int $timeout): void
    {
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake(function (Request $request, array $options) use ($http, $timeout): mixed {
            $this->assertSame($timeout, $options['timeout']);

            return $http->response(['data' => []]);
        });

        (new Samsara('test-token', $config, $http))->drivers()->query()->get();
        $http->assertSentCount(1);
    }

    #[Test]
    public function it_builds_an_http_client_without_a_container_or_injected_factory(): void
    {
        $this->assertInstanceOf(PendingRequest::class, (new Samsara)->client());
    }

    #[Test]
    public function it_caches_resource_instances(): void
    {
        $samsara = new Samsara('test-token');

        $drivers1 = $samsara->drivers();
        $drivers2 = $samsara->drivers();

        $this->assertSame($drivers1, $drivers2);
    }

    #[Test]
    public function it_can_be_created_using_static_make_method(): void
    {
        $samsara = Samsara::make('test-token');

        $this->assertInstanceOf(Samsara::class, $samsara);
    }

    #[Test]
    public function it_can_be_instantiated_with_token(): void
    {
        $samsara = new Samsara('test-token');

        $this->assertInstanceOf(Samsara::class, $samsara);
    }

    #[Test]
    public function it_can_be_instantiated_without_token(): void
    {
        $samsara = new Samsara;

        $this->assertInstanceOf(Samsara::class, $samsara);
    }

    #[Test]
    public function it_can_get_config_values(): void
    {
        $config = [
            'timeout'  => 60,
            'attempts' => 5,
            'per_page' => 50,
        ];

        $samsara = new Samsara('test-token', $config);

        $this->assertSame(60, $samsara->getConfig('timeout'));
        $this->assertSame(5, $samsara->getConfig('attempts'));
        $this->assertSame(50, $samsara->getConfig('per_page'));
    }

    #[Test]
    public function it_can_set_token_using_with_token_method(): void
    {
        $samsara = new Samsara;
        $result = $samsara->withToken('new-token');

        $this->assertNotSame($samsara, $result);
        $this->assertFalse($samsara->hasToken());
        $this->assertTrue($result->hasToken());
    }

    #[Test]
    public function it_can_switch_to_canada_endpoint_and_send_requests_there(): void
    {
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake(['*' => $http->response(['data' => []])]);
        $samsara = new Samsara('test-token', ['attempts' => 1]);
        $samsara->setHttpFactory($http);

        $result = $samsara->withRegion('ca');
        $result->drivers()->query()->get();

        $this->assertNotSame($samsara, $result);
        $this->assertSame('https://api.samsara.com', $samsara->getBaseUrl());
        $this->assertNotSame($samsara->drivers(), $result->drivers());
        $this->assertSame('https://api.ca.samsara.com', $result->getBaseUrl());
        $http->assertSent(fn (Request $request): bool => str_starts_with(
            $request->url(),
            'https://api.ca.samsara.com/fleet/drivers'
        ));
    }

    #[Test]
    public function it_can_switch_to_eu_endpoint(): void
    {
        $samsara = new Samsara('test-token');
        $result = $samsara->withRegion('eu');

        $this->assertNotSame($samsara, $result);
        $this->assertSame('https://api.samsara.com', $samsara->getBaseUrl());
        $this->assertNotSame($samsara->drivers(), $result->drivers());
        $this->assertSame('https://api.eu.samsara.com', $result->getBaseUrl());
    }

    #[Test]
    public function it_can_use_a_custom_base_url(): void
    {
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake(['*' => $http->response(['data' => []])]);
        $samsara = new Samsara('test-token', ['attempts' => 1]);
        $samsara->setHttpFactory($http);

        $result = $samsara->withBaseUrl('https://samsara-proxy.example.test/');
        $result->drivers()->query()->get();

        $this->assertNotSame($samsara, $result);
        $this->assertSame('https://api.samsara.com', $samsara->getBaseUrl());
        $this->assertNotSame($samsara->drivers(), $result->drivers());
        $this->assertSame('https://samsara-proxy.example.test', $result->getBaseUrl());
        $http->assertSent(fn (Request $request): bool => str_starts_with(
            $request->url(),
            'https://samsara-proxy.example.test/fleet/drivers'
        ));
    }

    #[Test]
    public function it_creates_a_tokenless_clone(): void
    {
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake(['*' => $http->response(['data' => []])]);
        $authenticated = new Samsara('base-token', ['attempts' => 1], $http);

        $tokenless = $authenticated->withoutToken();
        $tokenless->drivers()->query()->get();

        $this->assertNotSame($authenticated, $tokenless);
        $this->assertTrue($authenticated->hasToken());
        $this->assertFalse($tokenless->hasToken());
        $http->assertSent(fn (Request $request): bool => ! $request->hasHeader('Authorization'));
    }

    #[Test]
    #[DataProvider('blankRegions')]
    public function it_defaults_a_blank_configured_region_to_us(?string $region): void
    {
        $this->assertSame('https://api.samsara.com', (new Samsara(null, ['region' => $region]))->getBaseUrl());
    }

    #[Test]
    public function it_does_not_retry_non_retryable_http_statuses(): void
    {
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake(['*' => $http->response(['message' => 'Forbidden'], 403)]);
        $samsara = new Samsara('test-token', ['attempts' => 3]);
        $samsara->setHttpFactory($http);

        try {
            $samsara->tags()->find('tag-1');
            $this->fail('Expected an authorization exception.');
        } catch (AuthorizationException) {
            $http->assertSentCount(1);
        }
    }

    #[Test]
    public function it_does_not_retry_post_on_server_error_by_default(): void
    {
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake(['*' => $http->response([], 500)]);
        $client = new Samsara('test-token', ['attempts' => 3], $http);

        try {
            $client->drivers()->create(['name' => 'Driver']);
            $this->fail('Expected a server exception.');
        } catch (ServerException $exception) {
            $this->assertSame(500, $exception->getCode());
        }

        $http->assertSentCount(1);
        Sleep::assertNeverSlept();
    }

    #[Test]
    #[DataProvider('excessiveRetryAfterValues')]
    public function it_does_not_retry_when_retry_after_exceeds_the_ceiling(string $header): void
    {
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake(['*' => $http->response([], 429, ['Retry-After' => $header])]);
        $client = new Samsara('test-token', ['attempts' => 2, 'retry_after_max_seconds' => 30], $http);

        try {
            $client->drivers()->query()->get();
            $this->fail('Expected a rate-limit exception.');
        } catch (RateLimitException $exception) {
            $this->assertSame(429, $exception->getCode());
        }

        $http->assertSentCount(1);
        Sleep::assertNeverSlept();
    }

    #[Test]
    public function it_gets_the_current_organization_from_me(): void
    {
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake(['*' => $http->response(['data' => [
            'id'   => 'org-1',
            'name' => 'Example Fleet',
        ]])]);
        $samsara = new Samsara('test-token', ['attempts' => 1], $http);

        $organization = $samsara->me();

        $this->assertInstanceOf(Organization::class, $organization);
        $this->assertSame('org-1', $organization->id);
        $http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/me');
    }

    #[Test]
    #[DataProvider('retryAfterValues')]
    public function it_honors_retry_after_values(?string $header, int $milliseconds): void
    {
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake(['*' => $http->sequence()
            ->push([], 429, $header === null ? [] : ['Retry-After' => $header])
            ->push(['data' => [['id' => 'recovered-driver']]])]);
        $client = new Samsara('test-token', ['attempts' => 2, 'retry_after_max_seconds' => 30], $http);

        $this->assertSame('recovered-driver', $client->drivers()->query()->first()->id);
        $http->assertSentCount(2);
        Sleep::assertSequence([Sleep::for($milliseconds)->milliseconds()]);
    }

    #[Test]
    public function it_inherits_retry_writes_from_config_in_make(): void
    {
        $this->app['config']->set('samsara.retry_writes', true);
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake(['*' => $http->sequence()->push([], 503)->push([], 200)]);
        $this->app->instance(HttpFactory::class, $http);

        $this->assertSame(200, Samsara::make('test-token')->client()->post('/fleet/drivers')->status());
        $http->assertSentCount(2);
    }

    #[Test]
    public function it_isolates_interleaved_tokens_and_regions(): void
    {
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake(['*' => $http->response(['data' => []])]);
        $base = new Samsara('base-token', ['attempts' => 1], $http);
        $first = $base->withToken('first-token');
        $second = $base->withToken('second-token');
        $firstQuery = $first->drivers()->query();
        $europe = $first->withRegion('eu');

        $second->drivers()->query()->get();
        $europe->drivers()->query()->get();
        $firstQuery->get();
        $base->drivers()->query()->get();
        $second->drivers()->query()->get();

        $http->assertSentCount(5);
        $this->assertSame([
            ['Bearer second-token', 'api.samsara.com'],
            ['Bearer first-token', 'api.eu.samsara.com'],
            ['Bearer first-token', 'api.samsara.com'],
            ['Bearer base-token', 'api.samsara.com'],
            ['Bearer second-token', 'api.samsara.com'],
        ], $http->recorded()->map(fn (array $pair): array => [
            $pair[0]->header('Authorization')[0] ?? null,
            parse_url($pair[0]->url(), PHP_URL_HOST),
        ])->all());
    }

    #[Test]
    public function it_isolates_tokens_and_cached_resources_between_client_clones(): void
    {
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake(['*' => $http->response(['data' => []])]);
        $base = new Samsara('base-token', ['attempts' => 1], $http);
        $baseDrivers = $base->drivers();

        $tenant = $base->withToken('tenant-token');
        $tenantDrivers = $tenant->drivers();
        $baseDrivers->query()->get();
        $tenantDrivers->query()->get();

        $this->assertNotSame($baseDrivers, $tenantDrivers);
        $http->assertSentInOrder([
            fn (Request $request): bool => $request->header('Authorization') === ['Bearer base-token'],
            fn (Request $request): bool => $request->header('Authorization') === ['Bearer tenant-token'],
        ]);
    }

    #[Test]
    public function it_omits_the_authorization_header_when_token_is_not_set(): void
    {
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake(['*' => $http->response(['data' => []])]);
        $samsara = new Samsara(null, ['attempts' => 1]);
        $samsara->setHttpFactory($http);

        $samsara->drivers()->query()->get();

        $http->assertSent(fn (Request $request): bool => ! $request->hasHeader('Authorization'));
    }

    #[Test]
    public function it_rejects_an_unknown_configured_region(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Samsara(null, ['region' => 'unknown']);
    }

    #[Test]
    public function it_rejects_an_unknown_region(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Samsara)->withRegion('unknown');
    }

    #[Test]
    #[DataProvider('invalidAttemptCounts')]
    public function it_rejects_attempt_counts_below_one(int $attempts): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Samsara('test-token', ['attempts' => $attempts]);
    }

    #[Test]
    #[DataProvider('readFailures')]
    public function it_retries_failed_reads_then_recovers(string $method, ?int $status): void
    {
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $sequence = $http->sequence();
        $status === null ? $sequence->pushFailedConnection() : $sequence->push([], $status);
        $sequence->push(['data' => [['id' => 'recovered-driver']]]);
        $http->fake(['*' => $sequence]);
        $client = new Samsara('test-token', ['attempts' => 2], $http);

        $response = $client->client()->send($method, '/fleet/drivers');

        $this->assertSame('recovered-driver', $response->json('data.0.id'));
        $http->assertSentCount(2);
        Sleep::assertSequence([Sleep::for(100)->milliseconds()]);
    }

    #[Test]
    #[DataProvider('retryableMethods')]
    public function it_retries_rate_limited_requests_for_every_method(string $method): void
    {
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake(['*' => $http->sequence()->push([], 429, ['Retry-After' => '2'])->push([], 200)]);
        $client = new Samsara('test-token', ['attempts' => 2], $http);

        $this->assertSame(200, $client->client()->send($method, '/fleet/drivers')->status());
        $http->assertSentCount(2);
        Sleep::assertSequence([Sleep::for(2)->seconds()]);
    }

    #[Test]
    public function it_retries_rate_limits_using_retry_after(): void
    {
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake(['*' => $http->response(
            ['message' => 'Rate limited'],
            429,
            ['Retry-After' => '0']
        )]);
        $samsara = new Samsara('test-token', ['attempts' => 2]);
        $samsara->setHttpFactory($http);

        try {
            $samsara->tags()->find('tag-1');
            $this->fail('Expected a rate-limit exception.');
        } catch (RateLimitException $exception) {
            $this->assertSame(0, $exception->getRetryAfter());
            $http->assertSentCount(2);
        }
    }

    #[Test]
    #[DataProvider('writeFailures')]
    public function it_retries_writes_only_when_opted_in(string $method, ?int $status, bool $retryWrites): void
    {
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake(['*' => $status === null
            ? $http->failedConnection()
            : $http->response([], $status)]);
        $client = new Samsara('test-token', ['attempts' => 3, 'retry_writes' => $retryWrites], $http);

        try {
            $client->client()->send($method, '/fleet/drivers');
            $this->fail('Expected the request to fail.');
        } catch (ConnectionException|RequestException $exception) {
            $this->assertSame($status === null ? ConnectionException::class : RequestException::class, $exception::class);
        }

        $http->assertSentCount($retryWrites ? 3 : 1);
    }

    #[Test]
    public function it_returns_addresses_resource(): void
    {
        $samsara = new Samsara('test-token');

        $addresses = $samsara->addresses();

        $this->assertInstanceOf(AddressesResource::class, $addresses);
    }

    #[Test]
    public function it_returns_assets_resource(): void
    {
        $samsara = new Samsara('test-token');

        $assets = $samsara->assets();

        $this->assertInstanceOf(AssetsResource::class, $assets);
    }

    #[Test]
    public function it_returns_contacts_resource(): void
    {
        $samsara = new Samsara('test-token');

        $contacts = $samsara->contacts();

        $this->assertInstanceOf(ContactsResource::class, $contacts);
    }

    #[Test]
    public function it_returns_default_value_for_missing_config(): void
    {
        $samsara = new Samsara('test-token');

        $this->assertNull($samsara->getConfig('missing'));
        $this->assertSame('default', $samsara->getConfig('missing', 'default'));
    }

    #[Test]
    public function it_returns_drivers_resource(): void
    {
        $samsara = new Samsara('test-token');

        $drivers = $samsara->drivers();

        $this->assertInstanceOf(DriversResource::class, $drivers);
    }

    #[Test]
    public function it_returns_equipment_resource(): void
    {
        $samsara = new Samsara('test-token');

        $equipment = $samsara->equipment();

        $this->assertInstanceOf(EquipmentResource::class, $equipment);
    }

    #[Test]
    public function it_returns_false_when_token_is_not_set(): void
    {
        $samsara = new Samsara;

        $this->assertFalse($samsara->hasToken());
    }

    #[Test]
    public function it_returns_gateways_resource(): void
    {
        $samsara = new Samsara('test-token');

        $gateways = $samsara->gateways();

        $this->assertInstanceOf(GatewaysResource::class, $gateways);
    }

    #[Test]
    public function it_returns_hours_of_service_resource(): void
    {
        $samsara = new Samsara('test-token');

        $hos = $samsara->hoursOfService();

        $this->assertInstanceOf(HoursOfServiceResource::class, $hos);
    }

    #[Test]
    public function it_returns_industrial_resource(): void
    {
        $samsara = new Samsara('test-token');

        $industrial = $samsara->industrial();

        $this->assertInstanceOf(IndustrialResource::class, $industrial);
    }

    #[Test]
    public function it_returns_live_shares_resource(): void
    {
        $samsara = new Samsara('test-token');

        $liveShares = $samsara->liveShares();

        $this->assertInstanceOf(LiveSharingLinksResource::class, $liveShares);
    }

    #[Test]
    public function it_returns_maintenance_resource(): void
    {
        $samsara = new Samsara('test-token');

        $maintenance = $samsara->maintenance();

        $this->assertInstanceOf(MaintenanceResource::class, $maintenance);
    }

    #[Test]
    public function it_returns_pending_request_from_client_method(): void
    {
        $samsara = new Samsara('test-token');

        $client = $samsara->client();

        $this->assertInstanceOf(PendingRequest::class, $client);
    }

    #[Test]
    public function it_returns_routes_resource(): void
    {
        $samsara = new Samsara('test-token');

        $routes = $samsara->routes();

        $this->assertInstanceOf(RoutesResource::class, $routes);
    }

    #[Test]
    public function it_returns_safety_events_resource(): void
    {
        $samsara = new Samsara('test-token');

        $safetyEvents = $samsara->safetyEvents();

        $this->assertInstanceOf(SafetyEventsResource::class, $safetyEvents);
    }

    #[Test]
    public function it_returns_sensors_resource(): void
    {
        $samsara = new Samsara('test-token');

        $sensors = $samsara->sensors();

        $this->assertInstanceOf(SensorsResource::class, $sensors);
    }

    #[Test]
    public function it_returns_tags_resource(): void
    {
        $samsara = new Samsara('test-token');

        $tags = $samsara->tags();

        $this->assertInstanceOf(TagsResource::class, $tags);
    }

    #[Test]
    public function it_returns_trailers_resource(): void
    {
        $samsara = new Samsara('test-token');

        $trailers = $samsara->trailers();

        $this->assertInstanceOf(TrailersResource::class, $trailers);
    }

    #[Test]
    public function it_returns_trips_resource(): void
    {
        $samsara = new Samsara('test-token');

        $trips = $samsara->trips();

        $this->assertInstanceOf(TripsResource::class, $trips);
    }

    #[Test]
    public function it_returns_true_when_token_is_set(): void
    {
        $samsara = new Samsara('test-token');

        $this->assertTrue($samsara->hasToken());
    }

    #[Test]
    public function it_returns_users_resource(): void
    {
        $samsara = new Samsara('test-token');

        $users = $samsara->users();

        $this->assertInstanceOf(UsersResource::class, $users);
    }

    #[Test]
    public function it_returns_vehicle_locations_resource(): void
    {
        $samsara = new Samsara('test-token');

        $vehicleLocations = $samsara->vehicleLocations();

        $this->assertInstanceOf(VehicleLocationsResource::class, $vehicleLocations);
    }

    #[Test]
    public function it_returns_vehicle_stats_resource(): void
    {
        $samsara = new Samsara('test-token');

        $vehicleStats = $samsara->vehicleStats();

        $this->assertInstanceOf(VehicleStatsResource::class, $vehicleStats);
    }

    #[Test]
    public function it_returns_vehicles_resource(): void
    {
        $samsara = new Samsara('test-token');

        $vehicles = $samsara->vehicles();

        $this->assertInstanceOf(VehiclesResource::class, $vehicles);
    }

    #[Test]
    public function it_returns_webhooks_resource(): void
    {
        $samsara = new Samsara('test-token');

        $webhooks = $samsara->webhooks();

        $this->assertInstanceOf(WebhooksResource::class, $webhooks);
    }

    #[Test]
    public function it_seeds_make_from_the_configured_region(): void
    {
        $this->app['config']->set('samsara.region', 'eu');

        $samsara = Samsara::make('tenant-token');

        $this->assertSame('https://api.eu.samsara.com', $samsara->getBaseUrl());
    }

    #[Test]
    public function it_surfaces_connection_failure_after_exhausting_attempts(): void
    {
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake(['*' => $http->failedConnection()]);
        $client = new Samsara('test-token', ['attempts' => 3], $http);

        try {
            $client->drivers()->query()->get();
            $this->fail('Expected a connection exception.');
        } catch (ConnectionException $exception) {
            $this->assertSame(ConnectionException::class, $exception::class);
        }

        $http->assertSentCount(3);
    }

    #[Test]
    #[DataProvider('requestAttemptCounts')]
    public function it_treats_attempts_as_the_total_request_count(
        int $attempts,
        int $expectedRequests
    ): void {
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake(['*' => $http->response(['message' => 'Service unavailable'], 503)]);

        $samsara = new Samsara('test-token', ['attempts' => $attempts]);
        $samsara->setHttpFactory($http);

        try {
            $samsara->tags()->find('tag-1');
            $this->fail('Expected a server exception after exhausting all attempts.');
        } catch (ServerException) {
            $http->assertSentCount($expectedRequests);
        }
    }

    #[Test]
    public function it_uses_us_base_url_by_default(): void
    {
        $samsara = new Samsara('test-token');

        $this->assertSame('https://api.samsara.com', $samsara->getBaseUrl());
    }

    /** @return array<string, array{?string}> */
    public static function blankRegions(): array
    {
        return ['empty' => [''], 'null' => [null]];
    }

    /** @return array<string, array{string}> */
    public static function excessiveRetryAfterValues(): array
    {
        return [
            'seconds' => ['31'],
            'date'    => ['Tue, 01 Jan 2030 00:00:31 GMT'],
        ];
    }

    /**
     * @return array<string, array{int}>
     */
    public static function invalidAttemptCounts(): array
    {
        return [
            'zero'     => [0],
            'negative' => [-1],
        ];
    }

    /** @return iterable<string, array{string, int|null}> */
    public static function readFailures(): iterable
    {
        foreach (['GET', 'HEAD', 'OPTIONS'] as $method) {
            foreach ([500, 408, null] as $status) {
                yield "{$method} {$status}" => [$method, $status];
            }
        }
    }

    /**
     * @return array<string, array{int, int}>
     */
    public static function requestAttemptCounts(): array
    {
        return [
            'one attempt makes one request'      => [1, 1],
            'two attempts make two requests'     => [2, 2],
            'three attempts make three requests' => [3, 3],
        ];
    }

    /** @return array<string, array{string}> */
    public static function retryableMethods(): array
    {
        return [
            'get'  => ['GET'], 'head' => ['HEAD'], 'options' => ['OPTIONS'],
            'post' => ['POST'], 'patch' => ['PATCH'], 'put' => ['PUT'], 'delete' => ['DELETE'],
        ];
    }

    /** @return array<string, array{string|null, int}> */
    public static function retryAfterValues(): array
    {
        return [
            'seconds'         => ['2', 2000],
            'trimmed seconds' => [' 2 ', 2000],
            'at ceiling'      => ['30', 30000],
            'future date'     => ['Tue, 01 Jan 2030 00:00:10 GMT', 10000],
            'date at ceiling' => ['Tue, 01 Jan 2030 00:00:30 GMT', 30000],
            'past date'       => ['Mon, 31 Dec 2029 23:59:59 GMT', 0],
            'missing'         => [null, 100],
            'invalid'         => ['invalid', 100],
        ];
    }

    /** @return array<string, array{array<string, int>, int}> */
    public static function timeouts(): array
    {
        return ['default' => [[], 30], 'configured' => [['timeout' => 60], 60]];
    }

    /** @return iterable<string, array{string, int|null, bool}> */
    public static function writeFailures(): iterable
    {
        foreach (['POST', 'PATCH', 'PUT', 'DELETE'] as $method) {
            foreach ([500, 503, 408, null] as $status) {
                yield "{$method} {$status} default" => [$method, $status, false];
                yield "{$method} {$status} opted in" => [$method, $status, true];
            }
        }
    }
}
