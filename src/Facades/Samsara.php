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
 * @method static SamsaraClient withToken(string $token)
 * @method static bool hasToken()
 * @method static string getBaseUrl()
 * @method static SamsaraClient useEuEndpoint()
 * @method static SamsaraClient useUsEndpoint()
 * @method static PendingRequest client()
 * @method static mixed getConfig(string $key, mixed $default = null)
 *
 * Fleet Resources:
 * @method static \Samsara\Resources\Fleet\DriversResource drivers()
 * @method static \Samsara\Resources\Fleet\VehiclesResource vehicles()
 * @method static \Samsara\Resources\Fleet\TrailersResource trailers()
 * @method static \Samsara\Resources\Fleet\EquipmentResource equipment()
 *
 * Telematics Resources:
 * @method static \Samsara\Resources\Telematics\VehicleStatsResource vehicleStats()
 * @method static \Samsara\Resources\Telematics\VehicleLocationsResource vehicleLocations()
 * @method static \Samsara\Resources\Telematics\TripsResource trips()
 *
 * Safety Resources:
 * @method static \Samsara\Resources\Safety\HoursOfServiceResource hoursOfService()
 * @method static \Samsara\Resources\Safety\MaintenanceResource maintenance()
 * @method static \Samsara\Resources\Safety\SafetyEventsResource safetyEvents()
 *
 * Dispatch Resources:
 * @method static \Samsara\Resources\Dispatch\RoutesResource routes()
 * @method static \Samsara\Resources\Dispatch\AddressesResource addresses()
 *
 * Organization Resources:
 * @method static \Samsara\Resources\Organization\UsersResource users()
 * @method static \Samsara\Resources\Organization\TagsResource tags()
 * @method static \Samsara\Resources\Organization\ContactsResource contacts()
 *
 * Industrial Resources:
 * @method static \Samsara\Resources\Industrial\IndustrialResource industrial()
 * @method static \Samsara\Resources\Industrial\SensorsResource sensors()
 * @method static \Samsara\Resources\Industrial\AssetsResource assets()
 *
 * Integrations Resources:
 * @method static \Samsara\Resources\Integrations\WebhooksResource webhooks()
 * @method static \Samsara\Resources\Integrations\GatewaysResource gateways()
 * @method static \Samsara\Resources\Integrations\LiveSharingLinksResource liveShares()
 *
 * Additional Resources:
 * @method static \Samsara\Resources\Additional\AlertsResource alerts()
 * @method static \Samsara\Resources\Additional\FormsResource forms()
 * @method static \Samsara\Resources\Additional\IftaResource ifta()
 * @method static \Samsara\Resources\Additional\IdlingResource idling()
 * @method static \Samsara\Resources\Additional\IssuesResource issues()
 * @method static \Samsara\Resources\Additional\FuelAndEnergyResource fuelAndEnergy()
 * @method static \Samsara\Resources\Additional\TachographResource tachograph()
 * @method static \Samsara\Resources\Additional\SpeedingResource speeding()
 * @method static \Samsara\Resources\Additional\CarrierProposedAssignmentsResource carrierProposedAssignments()
 * @method static \Samsara\Resources\Additional\DriverVehicleAssignmentsResource driverVehicleAssignments()
 * @method static \Samsara\Resources\Additional\DriverTrailerAssignmentsResource driverTrailerAssignments()
 * @method static \Samsara\Resources\Additional\TrailerAssignmentsResource trailerAssignments()
 * @method static \Samsara\Resources\Additional\WorkOrdersResource workOrders()
 * @method static \Samsara\Resources\Additional\CameraMediaResource cameraMedia()
 * @method static \Samsara\Resources\Additional\HubsResource hubs()
 * @method static \Samsara\Resources\Additional\SettingsResource settings()
 * @method static \Samsara\Resources\Additional\RouteEventsResource routeEvents()
 *
 * Beta/Preview/Legacy Resources:
 * @method static \Samsara\Resources\Beta\BetaResource beta()
 * @method static \Samsara\Resources\Preview\PreviewResource preview()
 * @method static \Samsara\Resources\Legacy\LegacyResource legacy()
 *
 * @see \Samsara\Samsara
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class Samsara extends Facade
{
    /**
     * Create a new SamsaraFake instance for testing.
     */
    public static function fake(): SamsaraFake
    {
        return SamsaraFake::create();
    }

    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return SamsaraClient::class;
    }
}
