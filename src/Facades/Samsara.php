<?php

namespace Samsara\Facades;

use Samsara\Testing\SamsaraFake;
use Samsara\Samsara as SamsaraClient;
use Illuminate\Support\Facades\Facade;
use Illuminate\Http\Client\PendingRequest;

/**
 * Samsara facade.
 *
 * Core Methods:
 *
 * @method static PendingRequest client()
 * @method static string getBaseUrl()
 * @method static mixed getConfig(string $key, mixed $default = null)
 * @method static \Illuminate\Http\Client\Factory getHttpFactory()
 * @method static bool hasToken()
 * @method static SamsaraClient make(?string $token = null, array<string, mixed> $config = [])
 * @method static \Samsara\Data\Organization\Organization me()
 * @method static SamsaraClient setHttpFactory(\Illuminate\Http\Client\Factory $http)
 * @method static SamsaraClient withBaseUrl(string $url)
 * @method static SamsaraClient withoutToken()
 * @method static SamsaraClient withRegion(string $region)
 * @method static SamsaraClient withToken(string $token)
 *
 * Fleet Resources:
 * @method static \Samsara\Resources\Fleet\DriversResource drivers()
 * @method static \Samsara\Resources\Fleet\EquipmentResource equipment()
 * @method static \Samsara\Resources\Fleet\TrailersResource trailers()
 * @method static \Samsara\Resources\Fleet\VehiclesResource vehicles()
 *
 * Telematics Resources:
 * @method static \Samsara\Resources\Telematics\TripsResource trips()
 * @method static \Samsara\Resources\Telematics\VehicleLocationsResource vehicleLocations()
 * @method static \Samsara\Resources\Telematics\VehicleStatsResource vehicleStats()
 *
 * Safety Resources:
 * @method static \Samsara\Resources\Safety\HoursOfServiceResource hoursOfService()
 * @method static \Samsara\Resources\Safety\MaintenanceResource maintenance()
 * @method static \Samsara\Resources\Safety\SafetyEventsResource safetyEvents()
 *
 * Dispatch Resources:
 * @method static \Samsara\Resources\Dispatch\AddressesResource addresses()
 * @method static \Samsara\Resources\Dispatch\RoutesResource routes()
 *
 * Organization Resources:
 * @method static \Samsara\Resources\Organization\ContactsResource contacts()
 * @method static \Samsara\Resources\Organization\TagsResource tags()
 * @method static \Samsara\Resources\Organization\UsersResource users()
 *
 * Industrial Resources:
 * @method static \Samsara\Resources\Industrial\AssetsResource assets()
 * @method static \Samsara\Resources\Industrial\IndustrialResource industrial()
 * @method static \Samsara\Resources\Industrial\SensorsResource sensors()
 *
 * Integrations Resources:
 * @method static \Samsara\Resources\Integrations\GatewaysResource gateways()
 * @method static \Samsara\Resources\Integrations\LiveSharingLinksResource liveShares()
 * @method static \Samsara\Resources\Integrations\WebhooksResource webhooks()
 *
 * Additional Resources:
 * @method static \Samsara\Resources\Additional\AlertsResource alerts()
 * @method static \Samsara\Resources\Additional\CameraMediaResource cameraMedia()
 * @method static \Samsara\Resources\Additional\CarrierProposedAssignmentsResource carrierProposedAssignments()
 * @method static \Samsara\Resources\Additional\DriverTrailerAssignmentsResource driverTrailerAssignments()
 * @method static \Samsara\Resources\Additional\DriverVehicleAssignmentsResource driverVehicleAssignments()
 * @method static \Samsara\Resources\Additional\FormsResource forms()
 * @method static \Samsara\Resources\Additional\FuelAndEnergyResource fuelAndEnergy()
 * @method static \Samsara\Resources\Additional\HubsResource hubs()
 * @method static \Samsara\Resources\Additional\IdlingResource idling()
 * @method static \Samsara\Resources\Additional\IftaResource ifta()
 * @method static \Samsara\Resources\Additional\IssuesResource issues()
 * @method static \Samsara\Resources\Additional\RouteEventsResource routeEvents()
 * @method static \Samsara\Resources\Additional\SettingsResource settings()
 * @method static \Samsara\Resources\Additional\SpeedingResource speeding()
 * @method static \Samsara\Resources\Additional\TachographResource tachograph()
 * @method static \Samsara\Resources\Additional\TrailerAssignmentsResource trailerAssignments()
 * @method static \Samsara\Resources\Additional\WorkOrdersResource workOrders()
 *
 * Beta/Preview/Legacy Resources:
 * @method static \Samsara\Resources\Beta\BetaResource beta()
 * @method static \Samsara\Resources\Legacy\LegacyResource legacy()
 * @method static \Samsara\Resources\Preview\PreviewResource preview()
 *
 * @see \Samsara\Samsara
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class Samsara extends Facade
{
    /**
     * Create a new SamsaraFake instance for testing.
     *
     * @param  array<string, array<mixed>>  $responses
     */
    public static function fake(array $responses = []): SamsaraFake
    {
        // Resolve deferred bindings before swapping in the fake.
        static::getFacadeRoot();

        $fake = SamsaraFake::create($responses);
        static::swap($fake);

        return $fake;
    }

    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return SamsaraClient::class;
    }
}
