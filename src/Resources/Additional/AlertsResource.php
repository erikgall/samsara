<?php

namespace Samsara\Resources\Additional;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Data\Alert\AlertIncident;
use Samsara\Data\Alert\AlertConfiguration;
use Samsara\Resources\Concerns\CreatesEntities;

/**
 * Alerts resource for the Samsara API.
 *
 * Provides access to alert configuration and incident endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @extends \Samsara\Resources\Resource<AlertConfiguration>
 */
class AlertsResource extends Resource
{
    /** @use CreatesEntities<AlertConfiguration> */
    use CreatesEntities;

    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/alerts/configurations';

    /**
     * The entity class for this resource.
     *
     * @var class-string<AlertConfiguration>
     */
    protected string $entity = AlertConfiguration::class;

    /**
     * Get a query builder for alert configurations.
     *
     * @return Builder<AlertConfiguration>
     */
    public function configurations(): Builder
    {
        return $this->query();
    }

    /**
     * Create a new alert configuration.
     *
     * @param  array<string, mixed>  $data
     */
    public function createConfiguration(array $data): AlertConfiguration
    {
        $response = $this->executeSdkRequest(
            fn () => $this->client()->post($this->endpoint, $data),
            $this->endpoint
        );

        return $this->mapToEntity($this->decodeJsonData($response, $this->endpoint));
    }

    /**
     * Delete alert configurations.
     */
    public function deleteConfigurations(string $id): bool
    {
        $response = $this->executeSdkRequest(
            fn () => $this->client()
                ->withQueryParameters(['id' => $id])
                ->delete($this->endpoint),
            $this->endpoint
        );

        return $response->successful();
    }

    /**
     * Get a query builder for alert incidents.
     *
     * @return Builder<AlertIncident>
     */
    public function incidents(): Builder
    {
        return $this->newQuery('/alerts/incidents/stream')
            ->withoutDefaultPageSize()
            ->repeatQueryParameters('configurationIds')
            ->usingEntity(AlertIncident::class);
    }

    /**
     * Create a new query builder for this resource.
     *
     * @return Builder<AlertConfiguration>
     */
    public function query(): Builder
    {
        return $this->newQuery()
            ->withoutDefaultPageSize()
            ->repeatQueryParameters('ids');
    }

    /**
     * Update an alert configuration.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateConfiguration(string $id, array $data): AlertConfiguration
    {
        $payload = [...$data, 'id' => $id];
        $response = $this->executeSdkRequest(
            fn () => $this->client()->patch($this->endpoint, $payload),
            $this->endpoint
        );

        return $this->mapToEntity($this->decodeJsonData($response, $this->endpoint));
    }
}
