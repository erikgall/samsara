<?php

namespace Samsara\Tests\Unit;

use Samsara\Samsara;
use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\PendingRequest;
use Samsara\Resources\Fleet\DriversResource;
use Samsara\Resources\Fleet\TrailersResource;
use Samsara\Resources\Fleet\VehiclesResource;
use Samsara\Resources\Dispatch\RoutesResource;
use Samsara\Resources\Fleet\EquipmentResource;
use Samsara\Resources\Telematics\TripsResource;
use Samsara\Resources\Industrial\AssetsResource;
use Samsara\Resources\Organization\TagsResource;
use Samsara\Resources\Dispatch\AddressesResource;
use Samsara\Resources\Industrial\SensorsResource;
use Samsara\Resources\Organization\UsersResource;
use Samsara\Resources\Safety\MaintenanceResource;
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
    public function it_accepts_config_array_in_constructor(): void
    {
        $config = [
            'timeout' => 60,
            'retry'   => 5,
        ];

        $samsara = new Samsara('test-token', $config);

        $this->assertInstanceOf(Samsara::class, $samsara);
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
            'retry'    => 5,
            'per_page' => 50,
        ];

        $samsara = new Samsara('test-token', $config);

        $this->assertSame(60, $samsara->getConfig('timeout'));
        $this->assertSame(5, $samsara->getConfig('retry'));
        $this->assertSame(50, $samsara->getConfig('per_page'));
    }

    #[Test]
    public function it_can_set_token_using_with_token_method(): void
    {
        $samsara = new Samsara;
        $result = $samsara->withToken('new-token');

        $this->assertSame($samsara, $result);
        $this->assertTrue($samsara->hasToken());
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
    public function it_uses_us_base_url_by_default(): void
    {
        $samsara = new Samsara('test-token');

        $this->assertSame('https://api.samsara.com', $samsara->getBaseUrl());
    }
}
