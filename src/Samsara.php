<?php

namespace ErikGall\Samsara;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Factory as HttpFactory;
use ErikGall\Samsara\Resources\Fleet\DriversResource;
use ErikGall\Samsara\Resources\Fleet\TrailersResource;
use ErikGall\Samsara\Resources\Fleet\VehiclesResource;
use ErikGall\Samsara\Resources\Additional\HubsResource;
use ErikGall\Samsara\Resources\Additional\IftaResource;
use ErikGall\Samsara\Resources\Dispatch\RoutesResource;
use ErikGall\Samsara\Resources\Fleet\EquipmentResource;
use ErikGall\Samsara\Resources\Additional\FormsResource;
use ErikGall\Samsara\Resources\Telematics\TripsResource;
use ErikGall\Samsara\Resources\Additional\AlertsResource;
use ErikGall\Samsara\Resources\Additional\IdlingResource;
use ErikGall\Samsara\Resources\Additional\IssuesResource;
use ErikGall\Samsara\Resources\Industrial\AssetsResource;
use ErikGall\Samsara\Resources\Organization\TagsResource;
use ErikGall\Samsara\Resources\Dispatch\AddressesResource;
use ErikGall\Samsara\Resources\Industrial\SensorsResource;
use ErikGall\Samsara\Resources\Organization\UsersResource;
use ErikGall\Samsara\Resources\Safety\MaintenanceResource;
use ErikGall\Samsara\Resources\Additional\SettingsResource;
use ErikGall\Samsara\Resources\Additional\SpeedingResource;
use ErikGall\Samsara\Resources\Safety\SafetyEventsResource;
use ErikGall\Samsara\Resources\Additional\TachographResource;
use ErikGall\Samsara\Resources\Additional\WorkOrdersResource;
use ErikGall\Samsara\Resources\Industrial\IndustrialResource;
use ErikGall\Samsara\Resources\Integrations\GatewaysResource;
use ErikGall\Samsara\Resources\Integrations\WebhooksResource;
use ErikGall\Samsara\Resources\Organization\ContactsResource;
use ErikGall\Samsara\Resources\Safety\HoursOfServiceResource;
use ErikGall\Samsara\Resources\Additional\CameraMediaResource;
use ErikGall\Samsara\Resources\Additional\RouteEventsResource;
use ErikGall\Samsara\Resources\Telematics\VehicleStatsResource;
use ErikGall\Samsara\Resources\Additional\FuelAndEnergyResource;
use ErikGall\Samsara\Resources\Telematics\VehicleLocationsResource;
use ErikGall\Samsara\Resources\Additional\TrailerAssignmentsResource;
use ErikGall\Samsara\Resources\Integrations\LiveSharingLinksResource;
use ErikGall\Samsara\Resources\Additional\DriverTrailerAssignmentsResource;
use ErikGall\Samsara\Resources\Additional\DriverVehicleAssignmentsResource;
use ErikGall\Samsara\Resources\Additional\CarrierProposedAssignmentsResource;

/**
 * Samsara API client.
 *
 * Main entry point for interacting with the Samsara Fleet Management API.
 * Provides a fluent, Laravel-style interface for API operations.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class Samsara
{
    /**
     * The base URL for the EU region API.
     */
    protected const EU_BASE_URL = 'https://api.eu.samsara.com';

    /**
     * The base URL for the US region API.
     */
    protected const US_BASE_URL = 'https://api.samsara.com';

    /**
     * The API base URL.
     */
    protected string $baseUrl = self::US_BASE_URL;

    /**
     * The configuration array.
     *
     * @var array<string, mixed>
     */
    protected array $config = [];

    /**
     * The HTTP client factory.
     */
    protected HttpFactory $http;

    /**
     * Cached resource instances.
     *
     * @var array<string, object>
     */
    protected array $resources = [];

    /**
     * The API token.
     */
    protected ?string $token = null;

    /**
     * Create a new Samsara client instance.
     *
     * @param  string|null  $token  The API token
     * @param  array<string, mixed>  $config  Configuration options
     */
    public function __construct(?string $token = null, array $config = [])
    {
        $this->token = $token;
        $this->config = $config;
        $this->http = new HttpFactory;
    }

    /**
     * Get the AddressesResource.
     */
    public function addresses(): AddressesResource
    {
        /** @var AddressesResource */
        return $this->resource(AddressesResource::class);
    }

    /**
     * Get the AlertsResource.
     */
    public function alerts(): AlertsResource
    {
        /** @var AlertsResource */
        return $this->resource(AlertsResource::class);
    }

    /**
     * Get the AssetsResource.
     */
    public function assets(): AssetsResource
    {
        /** @var AssetsResource */
        return $this->resource(AssetsResource::class);
    }

    /**
     * Get the CameraMediaResource.
     */
    public function cameraMedia(): CameraMediaResource
    {
        /** @var CameraMediaResource */
        return $this->resource(CameraMediaResource::class);
    }

    /**
     * Get the CarrierProposedAssignmentsResource.
     */
    public function carrierProposedAssignments(): CarrierProposedAssignmentsResource
    {
        /** @var CarrierProposedAssignmentsResource */
        return $this->resource(CarrierProposedAssignmentsResource::class);
    }

    /**
     * Get a configured HTTP client.
     */
    public function client(): PendingRequest
    {
        return $this->http->baseUrl($this->baseUrl)
            ->withToken($this->token ?? '')
            ->acceptJson()
            ->asJson()
            ->timeout($this->getConfig('timeout', 30))
            ->retry($this->getConfig('retry', 3), 100);
    }

    /**
     * Get the ContactsResource.
     */
    public function contacts(): ContactsResource
    {
        /** @var ContactsResource */
        return $this->resource(ContactsResource::class);
    }

    /**
     * Get the DriversResource.
     */
    public function drivers(): DriversResource
    {
        /** @var DriversResource */
        return $this->resource(DriversResource::class);
    }

    /**
     * Get the DriverTrailerAssignmentsResource.
     */
    public function driverTrailerAssignments(): DriverTrailerAssignmentsResource
    {
        /** @var DriverTrailerAssignmentsResource */
        return $this->resource(DriverTrailerAssignmentsResource::class);
    }

    /**
     * Get the DriverVehicleAssignmentsResource.
     */
    public function driverVehicleAssignments(): DriverVehicleAssignmentsResource
    {
        /** @var DriverVehicleAssignmentsResource */
        return $this->resource(DriverVehicleAssignmentsResource::class);
    }

    /**
     * Get the EquipmentResource.
     */
    public function equipment(): EquipmentResource
    {
        /** @var EquipmentResource */
        return $this->resource(EquipmentResource::class);
    }

    /**
     * Get the FormsResource.
     */
    public function forms(): FormsResource
    {
        /** @var FormsResource */
        return $this->resource(FormsResource::class);
    }

    /**
     * Get the FuelAndEnergyResource.
     */
    public function fuelAndEnergy(): FuelAndEnergyResource
    {
        /** @var FuelAndEnergyResource */
        return $this->resource(FuelAndEnergyResource::class);
    }

    /**
     * Get the GatewaysResource.
     */
    public function gateways(): GatewaysResource
    {
        /** @var GatewaysResource */
        return $this->resource(GatewaysResource::class);
    }

    /**
     * Get the current base URL.
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Get a configuration value.
     *
     * @param  string  $key  The configuration key
     * @param  mixed  $default  The default value if key doesn't exist
     * @return mixed
     */
    public function getConfig(string $key, mixed $default = null): mixed
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * Get the HTTP factory instance.
     */
    public function getHttpFactory(): HttpFactory
    {
        return $this->http;
    }

    /**
     * Determine if a token has been set.
     */
    public function hasToken(): bool
    {
        return $this->token !== null;
    }

    /**
     * Get the HoursOfServiceResource.
     */
    public function hoursOfService(): HoursOfServiceResource
    {
        /** @var HoursOfServiceResource */
        return $this->resource(HoursOfServiceResource::class);
    }

    /**
     * Get the HubsResource.
     */
    public function hubs(): HubsResource
    {
        /** @var HubsResource */
        return $this->resource(HubsResource::class);
    }

    /**
     * Get the IdlingResource.
     */
    public function idling(): IdlingResource
    {
        /** @var IdlingResource */
        return $this->resource(IdlingResource::class);
    }

    /**
     * Get the IftaResource.
     */
    public function ifta(): IftaResource
    {
        /** @var IftaResource */
        return $this->resource(IftaResource::class);
    }

    /**
     * Get the IndustrialResource.
     */
    public function industrial(): IndustrialResource
    {
        /** @var IndustrialResource */
        return $this->resource(IndustrialResource::class);
    }

    /**
     * Get the IssuesResource.
     */
    public function issues(): IssuesResource
    {
        /** @var IssuesResource */
        return $this->resource(IssuesResource::class);
    }

    /**
     * Get the LiveSharingLinksResource.
     */
    public function liveShares(): LiveSharingLinksResource
    {
        /** @var LiveSharingLinksResource */
        return $this->resource(LiveSharingLinksResource::class);
    }

    /**
     * Get the MaintenanceResource.
     */
    public function maintenance(): MaintenanceResource
    {
        /** @var MaintenanceResource */
        return $this->resource(MaintenanceResource::class);
    }

    /**
     * Get the RouteEventsResource.
     */
    public function routeEvents(): RouteEventsResource
    {
        /** @var RouteEventsResource */
        return $this->resource(RouteEventsResource::class);
    }

    /**
     * Get the RoutesResource.
     */
    public function routes(): RoutesResource
    {
        /** @var RoutesResource */
        return $this->resource(RoutesResource::class);
    }

    /**
     * Get the SafetyEventsResource.
     */
    public function safetyEvents(): SafetyEventsResource
    {
        /** @var SafetyEventsResource */
        return $this->resource(SafetyEventsResource::class);
    }

    /**
     * Get the SensorsResource (Legacy v1).
     */
    public function sensors(): SensorsResource
    {
        /** @var SensorsResource */
        return $this->resource(SensorsResource::class);
    }

    /**
     * Set the HTTP factory instance.
     *
     * Useful for testing.
     *
     * @param  HttpFactory  $http  The HTTP factory
     */
    public function setHttpFactory(HttpFactory $http): static
    {
        $this->http = $http;

        return $this;
    }

    /**
     * Get the SettingsResource.
     */
    public function settings(): SettingsResource
    {
        /** @var SettingsResource */
        return $this->resource(SettingsResource::class);
    }

    /**
     * Get the SpeedingResource.
     */
    public function speeding(): SpeedingResource
    {
        /** @var SpeedingResource */
        return $this->resource(SpeedingResource::class);
    }

    /**
     * Get the TachographResource.
     */
    public function tachograph(): TachographResource
    {
        /** @var TachographResource */
        return $this->resource(TachographResource::class);
    }

    /**
     * Get the TagsResource.
     */
    public function tags(): TagsResource
    {
        /** @var TagsResource */
        return $this->resource(TagsResource::class);
    }

    /**
     * Get the TrailerAssignmentsResource.
     */
    public function trailerAssignments(): TrailerAssignmentsResource
    {
        /** @var TrailerAssignmentsResource */
        return $this->resource(TrailerAssignmentsResource::class);
    }

    /**
     * Get the TrailersResource.
     */
    public function trailers(): TrailersResource
    {
        /** @var TrailersResource */
        return $this->resource(TrailersResource::class);
    }

    /**
     * Get the TripsResource.
     */
    public function trips(): TripsResource
    {
        /** @var TripsResource */
        return $this->resource(TripsResource::class);
    }

    /**
     * Switch to the EU API endpoint.
     */
    public function useEuEndpoint(): static
    {
        $this->baseUrl = self::EU_BASE_URL;

        return $this;
    }

    /**
     * Get the UsersResource.
     */
    public function users(): UsersResource
    {
        /** @var UsersResource */
        return $this->resource(UsersResource::class);
    }

    /**
     * Switch to the US API endpoint.
     */
    public function useUsEndpoint(): static
    {
        $this->baseUrl = self::US_BASE_URL;

        return $this;
    }

    /**
     * Get the VehicleLocationsResource.
     */
    public function vehicleLocations(): VehicleLocationsResource
    {
        /** @var VehicleLocationsResource */
        return $this->resource(VehicleLocationsResource::class);
    }

    /**
     * Get the VehiclesResource.
     */
    public function vehicles(): VehiclesResource
    {
        /** @var VehiclesResource */
        return $this->resource(VehiclesResource::class);
    }

    /**
     * Get the VehicleStatsResource.
     */
    public function vehicleStats(): VehicleStatsResource
    {
        /** @var VehicleStatsResource */
        return $this->resource(VehicleStatsResource::class);
    }

    /**
     * Get the WebhooksResource.
     */
    public function webhooks(): WebhooksResource
    {
        /** @var WebhooksResource */
        return $this->resource(WebhooksResource::class);
    }

    /**
     * Set the API token.
     *
     * @param  string  $token  The API token
     */
    public function withToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get the WorkOrdersResource.
     */
    public function workOrders(): WorkOrdersResource
    {
        /** @var WorkOrdersResource */
        return $this->resource(WorkOrdersResource::class);
    }

    /**
     * Create a new Samsara client instance.
     *
     * @param  string|null  $token  The API token
     * @param  array<string, mixed>  $config  Configuration options
     */
    public static function make(?string $token = null, array $config = []): static
    {
        /** @phpstan-ignore new.static */
        return new static($token, $config);
    }

    /**
     * Get or create a cached resource instance.
     *
     * @template T of object
     *
     * @param  class-string<T>  $class  The resource class
     * @return T
     */
    protected function resource(string $class): object
    {
        if (! isset($this->resources[$class])) {
            $this->resources[$class] = new $class($this);
        }

        /** @phpstan-ignore return.type */
        return $this->resources[$class];
    }
}
