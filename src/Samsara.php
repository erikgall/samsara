<?php

namespace ErikGall\Samsara;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Factory as HttpFactory;
use ErikGall\Samsara\Resources\Fleet\DriversResource;
use ErikGall\Samsara\Resources\Fleet\TrailersResource;
use ErikGall\Samsara\Resources\Fleet\VehiclesResource;
use ErikGall\Samsara\Resources\Dispatch\RoutesResource;
use ErikGall\Samsara\Resources\Fleet\EquipmentResource;
use ErikGall\Samsara\Resources\Telematics\TripsResource;
use ErikGall\Samsara\Resources\Industrial\AssetsResource;
use ErikGall\Samsara\Resources\Organization\TagsResource;
use ErikGall\Samsara\Resources\Dispatch\AddressesResource;
use ErikGall\Samsara\Resources\Industrial\SensorsResource;
use ErikGall\Samsara\Resources\Organization\UsersResource;
use ErikGall\Samsara\Resources\Safety\MaintenanceResource;
use ErikGall\Samsara\Resources\Safety\SafetyEventsResource;
use ErikGall\Samsara\Resources\Industrial\IndustrialResource;
use ErikGall\Samsara\Resources\Organization\ContactsResource;
use ErikGall\Samsara\Resources\Safety\HoursOfServiceResource;
use ErikGall\Samsara\Resources\Telematics\VehicleStatsResource;
use ErikGall\Samsara\Resources\Telematics\VehicleLocationsResource;

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
     * Get the AssetsResource.
     */
    public function assets(): AssetsResource
    {
        /** @var AssetsResource */
        return $this->resource(AssetsResource::class);
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
     * Get the EquipmentResource.
     */
    public function equipment(): EquipmentResource
    {
        /** @var EquipmentResource */
        return $this->resource(EquipmentResource::class);
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
     * Get the IndustrialResource.
     */
    public function industrial(): IndustrialResource
    {
        /** @var IndustrialResource */
        return $this->resource(IndustrialResource::class);
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
     * Get the TagsResource.
     */
    public function tags(): TagsResource
    {
        /** @var TagsResource */
        return $this->resource(TagsResource::class);
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
