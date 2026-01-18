<?php

namespace ErikGall\Samsara\Facades;

use Illuminate\Support\Facades\Facade;
use ErikGall\Samsara\Testing\SamsaraFake;
use Illuminate\Http\Client\PendingRequest;
use ErikGall\Samsara\Samsara as SamsaraClient;

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
 * @method static \ErikGall\Samsara\Resources\Fleet\DriversResource drivers()
 * @method static \ErikGall\Samsara\Resources\Fleet\VehiclesResource vehicles()
 * @method static \ErikGall\Samsara\Resources\Fleet\TrailersResource trailers()
 * @method static \ErikGall\Samsara\Resources\Fleet\EquipmentResource equipment()
 *
 * Telematics Resources:
 * @method static \ErikGall\Samsara\Resources\Telematics\VehicleStatsResource vehicleStats()
 * @method static \ErikGall\Samsara\Resources\Telematics\VehicleLocationsResource vehicleLocations()
 * @method static \ErikGall\Samsara\Resources\Telematics\TripsResource trips()
 *
 * Safety Resources:
 * @method static \ErikGall\Samsara\Resources\Safety\HoursOfServiceResource hoursOfService()
 * @method static \ErikGall\Samsara\Resources\Safety\MaintenanceResource maintenance()
 * @method static \ErikGall\Samsara\Resources\Safety\SafetyEventsResource safetyEvents()
 *
 * Dispatch Resources:
 * @method static \ErikGall\Samsara\Resources\Dispatch\RoutesResource routes()
 * @method static \ErikGall\Samsara\Resources\Dispatch\AddressesResource addresses()
 *
 * Organization Resources:
 * @method static \ErikGall\Samsara\Resources\Organization\UsersResource users()
 * @method static \ErikGall\Samsara\Resources\Organization\TagsResource tags()
 * @method static \ErikGall\Samsara\Resources\Organization\ContactsResource contacts()
 *
 * Industrial Resources:
 * @method static \ErikGall\Samsara\Resources\Industrial\IndustrialResource industrial()
 * @method static \ErikGall\Samsara\Resources\Industrial\SensorsResource sensors()
 * @method static \ErikGall\Samsara\Resources\Industrial\AssetsResource assets()
 *
 * Integrations Resources:
 * @method static \ErikGall\Samsara\Resources\Integrations\WebhooksResource webhooks()
 * @method static \ErikGall\Samsara\Resources\Integrations\GatewaysResource gateways()
 * @method static \ErikGall\Samsara\Resources\Integrations\LiveSharingLinksResource liveShares()
 *
 * Additional Resources:
 * @method static \ErikGall\Samsara\Resources\Additional\AlertsResource alerts()
 * @method static \ErikGall\Samsara\Resources\Additional\FormsResource forms()
 * @method static \ErikGall\Samsara\Resources\Additional\IftaResource ifta()
 * @method static \ErikGall\Samsara\Resources\Additional\IdlingResource idling()
 * @method static \ErikGall\Samsara\Resources\Additional\IssuesResource issues()
 * @method static \ErikGall\Samsara\Resources\Additional\FuelAndEnergyResource fuelAndEnergy()
 * @method static \ErikGall\Samsara\Resources\Additional\TachographResource tachograph()
 * @method static \ErikGall\Samsara\Resources\Additional\SpeedingResource speeding()
 * @method static \ErikGall\Samsara\Resources\Additional\CarrierProposedAssignmentsResource carrierProposedAssignments()
 * @method static \ErikGall\Samsara\Resources\Additional\DriverVehicleAssignmentsResource driverVehicleAssignments()
 * @method static \ErikGall\Samsara\Resources\Additional\DriverTrailerAssignmentsResource driverTrailerAssignments()
 * @method static \ErikGall\Samsara\Resources\Additional\TrailerAssignmentsResource trailerAssignments()
 * @method static \ErikGall\Samsara\Resources\Additional\WorkOrdersResource workOrders()
 * @method static \ErikGall\Samsara\Resources\Additional\CameraMediaResource cameraMedia()
 * @method static \ErikGall\Samsara\Resources\Additional\HubsResource hubs()
 * @method static \ErikGall\Samsara\Resources\Additional\SettingsResource settings()
 * @method static \ErikGall\Samsara\Resources\Additional\RouteEventsResource routeEvents()
 *
 * Beta/Preview/Legacy Resources:
 * @method static \ErikGall\Samsara\Resources\Beta\BetaResource beta()
 * @method static \ErikGall\Samsara\Resources\Preview\PreviewResource preview()
 * @method static \ErikGall\Samsara\Resources\Legacy\LegacyResource legacy()
 *
 * @see \ErikGall\Samsara\Samsara
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
