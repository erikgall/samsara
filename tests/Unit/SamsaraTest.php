<?php

namespace Samsara\Tests\Unit;

use Samsara\Samsara;
use Samsara\Tests\TestCase;
use Illuminate\Support\Sleep;
use Illuminate\Http\Client\Request;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Exceptions\ServerException;
use Illuminate\Http\Client\PendingRequest;
use Samsara\Exceptions\RateLimitException;
use Samsara\Data\Organization\Organization;
use Samsara\Resources\Fleet\DriversResource;
use Samsara\Resources\Fleet\TrailersResource;
use Samsara\Resources\Fleet\VehiclesResource;
use PHPUnit\Framework\Attributes\DataProvider;
use Samsara\Exceptions\AuthorizationException;
use Samsara\Resources\Dispatch\RoutesResource;
use Samsara\Resources\Fleet\EquipmentResource;
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
    #[Test]
    public function it_accepts_an_http_factory_in_the_constructor(): void
    {
        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake(['*' => $http->response(['data' => []])]);

        $samsara = new Samsara('test-token', ['attempts' => 1], $http);
        $drivers = $samsara->drivers()->all();

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
        $http->fake(['*' => $http->response(['data' => []])]);
        $samsara = new Samsara('test-token', ['attempts' => 1]);
        $samsara->setHttpFactory($http);

        $result = $samsara->useCanadaEndpoint();
        $samsara->drivers()->query()->get();

        $this->assertSame($samsara, $result);
        $this->assertSame('https://api.ca.samsara.com', $samsara->getBaseUrl());
        $http->assertSent(fn ($request) => str_starts_with(
            $request->url(),
            'https://api.ca.samsara.com/fleet/drivers'
        ));
    }

    #[Test]
    public function it_can_switch_to_eu_endpoint(): void
    {
        $samsara = new Samsara('test-token');
        $result = $samsara->useEuEndpoint();

        $this->assertSame($samsara, $result);
        $this->assertSame('https://api.eu.samsara.com', $samsara->getBaseUrl());
    }

    #[Test]
    public function it_can_use_a_custom_base_url(): void
    {
        $http = new HttpFactory;
        $http->fake(['*' => $http->response(['data' => []])]);
        $samsara = new Samsara('test-token', ['attempts' => 1]);
        $samsara->setHttpFactory($http);

        $result = $samsara->useBaseUrl('https://samsara-proxy.example.test/');
        $samsara->drivers()->query()->get();

        $this->assertSame($samsara, $result);
        $this->assertSame('https://samsara-proxy.example.test', $samsara->getBaseUrl());
        $http->assertSent(fn ($request) => str_starts_with(
            $request->url(),
            'https://samsara-proxy.example.test/fleet/drivers'
        ));
    }

    #[Test]
    public function it_creates_a_tokenless_clone(): void
    {
        $http = new HttpFactory;
        $http->fake(['*' => $http->response(['data' => []])]);
        $authenticated = new Samsara('base-token', ['attempts' => 1], $http);

        $tokenless = $authenticated->withoutToken();
        $tokenless->drivers()->all();

        $this->assertNotSame($authenticated, $tokenless);
        $this->assertTrue($authenticated->hasToken());
        $this->assertFalse($tokenless->hasToken());
        $http->assertSent(fn (Request $request): bool => ! $request->hasHeader('Authorization'));
    }

    #[Test]
    public function it_does_not_expose_an_http_date_retry_after_as_zero_seconds(): void
    {
        $http = new HttpFactory;
        $http->fake(['*' => $http->response(
            ['message' => 'Rate limited'],
            429,
            ['Retry-After' => 'Thu, 01 Jan 1970 00:00:00 GMT']
        )]);
        $samsara = new Samsara('test-token', ['attempts' => 2]);
        $samsara->setHttpFactory($http);

        try {
            $samsara->tags()->find('tag-1');
            $this->fail('Expected a rate-limit exception.');
        } catch (RateLimitException $exception) {
            $this->assertNull($exception->getRetryAfter());
            $http->assertSentCount(2);
        }
    }

    #[Test]
    public function it_does_not_retry_non_retryable_http_statuses(): void
    {
        $http = new HttpFactory;
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
    public function it_does_not_retry_when_retry_after_exceeds_the_configured_ceiling(): void
    {
        Sleep::fake();

        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake(['*' => $http->response(
            ['message' => 'Rate limited'],
            429,
            ['Retry-After' => '3600'],
        )]);
        $samsara = new Samsara('test-token', [
            'attempts'                => 3,
            'retry_after_max_seconds' => 30,
        ]);
        $samsara->setHttpFactory($http);

        try {
            $samsara->tags()->find('tag-1');
            $this->fail('Expected a rate-limit exception.');
        } catch (RateLimitException $exception) {
            $this->assertSame(3600, $exception->getRetryAfter());
        }

        $http->assertSentCount(1);
        Sleep::assertNeverSlept();
    }

    #[Test]
    public function it_gets_the_current_organization_from_me(): void
    {
        $http = new HttpFactory;
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
    public function it_honors_small_retry_after_delta_seconds(): void
    {
        Sleep::fake();

        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake(['*' => $http->response(
            ['message' => 'Rate limited'],
            429,
            ['Retry-After' => '2'],
        )]);
        $samsara = new Samsara('test-token', [
            'attempts'                => 2,
            'retry_after_max_seconds' => 30,
        ]);
        $samsara->setHttpFactory($http);

        try {
            $samsara->tags()->find('tag-1');
            $this->fail('Expected a rate-limit exception.');
        } catch (RateLimitException $exception) {
            $this->assertSame(2, $exception->getRetryAfter());
        }

        $http->assertSentCount(2);
        Sleep::assertSequence([Sleep::for(2)->seconds()]);
    }

    #[Test]
    public function it_isolates_tokens_and_cached_resources_between_client_clones(): void
    {
        $http = new HttpFactory;
        $http->fake(['*' => $http->response(['data' => []])]);
        $base = new Samsara('base-token', ['attempts' => 1], $http);
        $baseDrivers = $base->drivers();

        $tenant = $base->withToken('tenant-token');
        $tenantDrivers = $tenant->drivers();
        $baseDrivers->all();
        $tenantDrivers->all();

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
        $http->fake(['*' => $http->response(['data' => []])]);
        $samsara = new Samsara(null, ['attempts' => 1]);
        $samsara->setHttpFactory($http);

        $samsara->drivers()->query()->get();

        $http->assertSent(fn ($request) => ! $request->hasHeader('Authorization'));
    }

    #[Test]
    #[DataProvider('invalidAttemptCounts')]
    public function it_rejects_attempt_counts_below_one(int $attempts): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Samsara request attempts must be at least 1.');

        new Samsara('test-token', ['attempts' => $attempts]);
    }

    #[Test]
    public function it_retries_rate_limits_using_retry_after(): void
    {
        $http = new HttpFactory;
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
    #[DataProvider('requestAttemptCounts')]
    public function it_treats_attempts_as_the_total_request_count(
        int $attempts,
        int $expectedRequests
    ): void {
        $http = new HttpFactory;
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
    public function it_trims_retry_after_delta_seconds(): void
    {
        Sleep::fake();

        $http = new HttpFactory;
        $http->preventStrayRequests();
        $http->fake(['*' => $http->response(
            ['message' => 'Rate limited'],
            429,
            ['Retry-After' => ' 2 '],
        )]);
        $samsara = new Samsara('test-token', [
            'attempts'                => 2,
            'retry_after_max_seconds' => 30,
        ]);
        $samsara->setHttpFactory($http);

        try {
            $samsara->tags()->find('tag-1');
            $this->fail('Expected a rate-limit exception.');
        } catch (RateLimitException $exception) {
            $this->assertSame(2, $exception->getRetryAfter());
        }

        $http->assertSentCount(2);
        Sleep::assertSequence([Sleep::for(2)->seconds()]);
    }

    #[Test]
    public function it_uses_us_base_url_by_default(): void
    {
        $samsara = new Samsara('test-token');

        $this->assertSame('https://api.samsara.com', $samsara->getBaseUrl());
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
}
