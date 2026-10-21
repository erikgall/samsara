<?php

namespace Samsara;

use Throwable;
use Illuminate\Support\Arr;
use Samsara\Http\RetryAfter;
use InvalidArgumentException;
use Illuminate\Container\Container;
use Illuminate\Http\Client\Request;
use Samsara\Resources\Beta\BetaResource;
use Illuminate\Http\Client\PendingRequest;
use Samsara\Data\Organization\Organization;
use Illuminate\Http\Client\RequestException;
use Samsara\Resources\Fleet\DriversResource;
use Samsara\Resources\Legacy\LegacyResource;
use Samsara\Resources\Fleet\TrailersResource;
use Samsara\Resources\Fleet\VehiclesResource;
use Samsara\Resources\Additional\HubsResource;
use Samsara\Resources\Additional\IftaResource;
use Samsara\Resources\Dispatch\RoutesResource;
use Samsara\Resources\Fleet\EquipmentResource;
use Samsara\Resources\Preview\PreviewResource;
use Illuminate\Http\Client\ConnectionException;
use Samsara\Resources\Additional\FormsResource;
use Samsara\Resources\Telematics\TripsResource;
use Samsara\Resources\Additional\AlertsResource;
use Samsara\Resources\Additional\IdlingResource;
use Samsara\Resources\Additional\IssuesResource;
use Samsara\Resources\Industrial\AssetsResource;
use Samsara\Resources\Organization\TagsResource;
use Samsara\Resources\Dispatch\AddressesResource;
use Samsara\Resources\Industrial\SensorsResource;
use Samsara\Resources\Organization\UsersResource;
use Samsara\Resources\Safety\MaintenanceResource;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Resources\Additional\SettingsResource;
use Samsara\Resources\Additional\SpeedingResource;
use Samsara\Resources\Safety\SafetyEventsResource;
use Samsara\Resources\Additional\TachographResource;
use Samsara\Resources\Additional\WorkOrdersResource;
use Samsara\Resources\Industrial\IndustrialResource;
use Samsara\Resources\Integrations\GatewaysResource;
use Samsara\Resources\Integrations\WebhooksResource;
use Samsara\Resources\Organization\ContactsResource;
use Samsara\Resources\Safety\HoursOfServiceResource;
use Samsara\Resources\Additional\CameraMediaResource;
use Samsara\Resources\Additional\RouteEventsResource;
use Samsara\Resources\Telematics\VehicleStatsResource;
use Samsara\Resources\Additional\FuelAndEnergyResource;
use Samsara\Resources\Organization\OrganizationResource;
use Samsara\Resources\Telematics\VehicleLocationsResource;
use Samsara\Resources\Additional\TrailerAssignmentsResource;
use Samsara\Resources\Integrations\LiveSharingLinksResource;
use Samsara\Resources\Additional\DriverTrailerAssignmentsResource;
use Samsara\Resources\Additional\DriverVehicleAssignmentsResource;
use Samsara\Resources\Additional\CarrierProposedAssignmentsResource;

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
     * The base URL for the Canada region API.
     */
    protected const CANADA_BASE_URL = 'https://api.ca.samsara.com';

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
     * @param  HttpFactory|null  $http  Optional HTTP factory for framework integration and testing
     */
    public function __construct(
        ?string $token = null,
        array $config = [],
        ?HttpFactory $http = null,
    ) {
        if ((int) ($config['attempts'] ?? 3) < 1) {
            throw new InvalidArgumentException('Samsara request attempts must be at least 1.');
        }

        $this->token = $token;
        $this->config = $config;
        $this->http = $http ?? new HttpFactory;

        $this->baseUrl = $this->regionBaseUrl(($config['region'] ?? null) ?: 'us');
    }

    /**
     * Get the AddressesResource.
     */
    public function addresses(): AddressesResource
    {
        return $this->resource(AddressesResource::class);
    }

    /**
     * Get the AlertsResource.
     */
    public function alerts(): AlertsResource
    {
        return $this->resource(AlertsResource::class);
    }

    /**
     * Get the AssetsResource.
     */
    public function assets(): AssetsResource
    {
        return $this->resource(AssetsResource::class);
    }

    /**
     * Get the BetaResource.
     *
     * Provides access to beta/preview endpoints that are not yet stable.
     */
    public function beta(): BetaResource
    {
        return $this->resource(BetaResource::class);
    }

    /**
     * Get the CameraMediaResource.
     */
    public function cameraMedia(): CameraMediaResource
    {
        return $this->resource(CameraMediaResource::class);
    }

    /**
     * Get the CarrierProposedAssignmentsResource.
     */
    public function carrierProposedAssignments(): CarrierProposedAssignmentsResource
    {
        return $this->resource(CarrierProposedAssignmentsResource::class);
    }

    /**
     * Get a configured HTTP client.
     */
    public function client(): PendingRequest
    {
        // Laravel 12.0 does not pass the request method to the retry "when" callback.
        $method = null;

        $request = $this->http->baseUrl($this->baseUrl)
            ->acceptJson()
            ->asJson()
            ->timeout($this->getConfig('timeout', 30))
            ->beforeSending(function (Request $request) use (&$method): void {
                $method = $request->method();
            })
            ->retry(
                $this->getConfig('attempts', 3),
                fn (int $attempt, mixed $exception): int => $this->retryDelayMilliseconds($exception),
                function (Throwable $exception) use (&$method): bool {
                    return $this->shouldRetry($exception, $method);
                },
            );

        if ($this->token !== null) {
            $request->withToken($this->token);
        }

        return $request;
    }

    /**
     * Get the ContactsResource.
     */
    public function contacts(): ContactsResource
    {
        return $this->resource(ContactsResource::class);
    }

    /**
     * Get the DriversResource.
     */
    public function drivers(): DriversResource
    {
        return $this->resource(DriversResource::class);
    }

    /**
     * Get the DriverTrailerAssignmentsResource.
     */
    public function driverTrailerAssignments(): DriverTrailerAssignmentsResource
    {
        return $this->resource(DriverTrailerAssignmentsResource::class);
    }

    /**
     * Get the DriverVehicleAssignmentsResource.
     */
    public function driverVehicleAssignments(): DriverVehicleAssignmentsResource
    {
        return $this->resource(DriverVehicleAssignmentsResource::class);
    }

    /**
     * Get the EquipmentResource.
     */
    public function equipment(): EquipmentResource
    {
        return $this->resource(EquipmentResource::class);
    }

    /**
     * Get the FormsResource.
     */
    public function forms(): FormsResource
    {
        return $this->resource(FormsResource::class);
    }

    /**
     * Get the FuelAndEnergyResource.
     */
    public function fuelAndEnergy(): FuelAndEnergyResource
    {
        return $this->resource(FuelAndEnergyResource::class);
    }

    /**
     * Get the GatewaysResource.
     */
    public function gateways(): GatewaysResource
    {
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
        return $this->resource(HoursOfServiceResource::class);
    }

    /**
     * Get the HubsResource.
     */
    public function hubs(): HubsResource
    {
        return $this->resource(HubsResource::class);
    }

    /**
     * Get the IdlingResource.
     */
    public function idling(): IdlingResource
    {
        return $this->resource(IdlingResource::class);
    }

    /**
     * Get the IftaResource.
     */
    public function ifta(): IftaResource
    {
        return $this->resource(IftaResource::class);
    }

    /**
     * Get the IndustrialResource.
     */
    public function industrial(): IndustrialResource
    {
        return $this->resource(IndustrialResource::class);
    }

    /**
     * Get the IssuesResource.
     */
    public function issues(): IssuesResource
    {
        return $this->resource(IssuesResource::class);
    }

    /**
     * Get the LegacyResource.
     *
     * Provides access to deprecated v1 endpoints.
     */
    public function legacy(): LegacyResource
    {
        return $this->resource(LegacyResource::class);
    }

    /**
     * Get the LiveSharingLinksResource.
     */
    public function liveShares(): LiveSharingLinksResource
    {
        return $this->resource(LiveSharingLinksResource::class);
    }

    /**
     * Get the MaintenanceResource.
     */
    public function maintenance(): MaintenanceResource
    {
        return $this->resource(MaintenanceResource::class);
    }

    /**
     * Get the organization associated with the current token.
     */
    public function me(): Organization
    {
        $resource = $this->resource(OrganizationResource::class);

        return $resource->current();
    }

    /**
     * Get the PreviewResource.
     *
     * Provides access to preview endpoints that are in early testing.
     */
    public function preview(): PreviewResource
    {
        return $this->resource(PreviewResource::class);
    }

    /**
     * Get the RouteEventsResource.
     */
    public function routeEvents(): RouteEventsResource
    {
        return $this->resource(RouteEventsResource::class);
    }

    /**
     * Get the RoutesResource.
     */
    public function routes(): RoutesResource
    {
        return $this->resource(RoutesResource::class);
    }

    /**
     * Get the SafetyEventsResource.
     */
    public function safetyEvents(): SafetyEventsResource
    {
        return $this->resource(SafetyEventsResource::class);
    }

    /**
     * Get the SensorsResource (Legacy v1).
     */
    public function sensors(): SensorsResource
    {
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
        return $this->resource(SettingsResource::class);
    }

    /**
     * Get the SpeedingResource.
     */
    public function speeding(): SpeedingResource
    {
        return $this->resource(SpeedingResource::class);
    }

    /**
     * Get the TachographResource.
     */
    public function tachograph(): TachographResource
    {
        return $this->resource(TachographResource::class);
    }

    /**
     * Get the TagsResource.
     */
    public function tags(): TagsResource
    {
        return $this->resource(TagsResource::class);
    }

    /**
     * Get the TrailerAssignmentsResource.
     */
    public function trailerAssignments(): TrailerAssignmentsResource
    {
        return $this->resource(TrailerAssignmentsResource::class);
    }

    /**
     * Get the TrailersResource.
     */
    public function trailers(): TrailersResource
    {
        return $this->resource(TrailersResource::class);
    }

    /**
     * Get the TripsResource.
     */
    public function trips(): TripsResource
    {
        return $this->resource(TripsResource::class);
    }

    /**
     * Get the UsersResource.
     */
    public function users(): UsersResource
    {
        return $this->resource(UsersResource::class);
    }

    /**
     * Get the VehicleLocationsResource.
     */
    public function vehicleLocations(): VehicleLocationsResource
    {
        return $this->resource(VehicleLocationsResource::class);
    }

    /**
     * Get the VehiclesResource.
     */
    public function vehicles(): VehiclesResource
    {
        return $this->resource(VehiclesResource::class);
    }

    /**
     * Get the VehicleStatsResource.
     */
    public function vehicleStats(): VehicleStatsResource
    {
        return $this->resource(VehicleStatsResource::class);
    }

    /**
     * Get the WebhooksResource.
     */
    public function webhooks(): WebhooksResource
    {
        return $this->resource(WebhooksResource::class);
    }

    /**
     * Create an isolated client with a custom API base URL.
     */
    public function withBaseUrl(string $url): static
    {
        $client = clone $this;
        $client->baseUrl = rtrim($url, '/');
        $client->resources = [];

        return $client;
    }

    /**
     * Create an isolated client without an API token.
     */
    public function withoutToken(): static
    {
        $client = clone $this;
        $client->token = null;
        $client->resources = [];

        return $client;
    }

    /**
     * Create an isolated client for the us, eu, or ca API region.
     *
     * @throws InvalidArgumentException When the region is unknown.
     */
    public function withRegion(string $region): static
    {
        return $this->withBaseUrl($this->regionBaseUrl($region));
    }

    /**
     * Create an isolated client with the API token.
     *
     * @param  string  $token  The API token
     */
    public function withToken(string $token): static
    {
        $client = clone $this;
        $client->token = $token;
        $client->resources = [];

        return $client;
    }

    /**
     * Get the WorkOrdersResource.
     */
    public function workOrders(): WorkOrdersResource
    {
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
        $container = Container::getInstance();
        $defaults = [];
        $http = null;

        if ($container->bound('config')) {
            $packageConfig = (array) $container->make('config')->get('samsara', []);
            $defaults = Arr::only($packageConfig, [
                'region',
                'timeout',
                'attempts',
                'retry_writes',
                'retry_after_max_seconds',
                'per_page',
            ]);
        }

        if ($container->bound(HttpFactory::class)) {
            $http = $container->make(HttpFactory::class);
        }

        /** @phpstan-ignore new.static */
        return new static($token, array_replace($defaults, $config), $http);
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
        /** @phpstan-ignore return.type */
        return $this->resources[$class] ??= new $class($this);
    }

    /**
     * Resolve the API base URL for a supported region.
     */
    private function regionBaseUrl(string $region): string
    {
        return match ($region) {
            'us'    => self::US_BASE_URL,
            'eu'    => self::EU_BASE_URL,
            'ca'    => self::CANADA_BASE_URL,
            default => throw new InvalidArgumentException("Unsupported Samsara region [{$region}]."),
        };
    }

    /**
     * Determine whether a rate-limit delay is safe to honor synchronously.
     */
    private function retryAfterIsWithinCeiling(RequestException $exception): bool
    {
        $retryAfter = $this->retryAfterSeconds($exception);

        if ($retryAfter === null) {
            return true;
        }

        return $retryAfter <= (int) $this->getConfig('retry_after_max_seconds', 30);
    }

    /**
     * Parse a Retry-After header into seconds.
     */
    private function retryAfterSeconds(RequestException $exception): ?int
    {
        return RetryAfter::seconds($exception->response->header('Retry-After'));
    }

    /**
     * Calculate the delay before a retry attempt.
     */
    private function retryDelayMilliseconds(mixed $exception): int
    {
        if (! $exception instanceof RequestException || $exception->response->status() !== 429) {
            return 100;
        }

        $retryAfter = $this->retryAfterSeconds($exception);

        if ($retryAfter === null) {
            return 100;
        }

        return $retryAfter * 1000;
    }

    /**
     * Retry only transport failures and statuses that are safe to retry.
     */
    private function shouldRetry(Throwable $exception, ?string $method): bool
    {
        if ($exception instanceof RequestException && $exception->response->status() === 429) {
            return $this->retryAfterIsWithinCeiling($exception);
        }

        if (! $this->getConfig('retry_writes', false) && ! in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) {
            return false;
        }

        return match (true) {
            $exception instanceof ConnectionException => true,
            $exception instanceof RequestException    => $exception->response->status() === 408
                || $exception->response->serverError(),
            default => false,
        };
    }
}
