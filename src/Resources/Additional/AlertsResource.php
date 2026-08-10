<?php

namespace Samsara\Resources\Additional;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Data\Alert\AlertIncident;
use Samsara\Data\Alert\AlertConfiguration;

/**
 * Alerts resource for the Samsara API.
 *
 * Provides access to alert configuration and incident endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class AlertsResource extends Resource
{
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

        /** @var AlertConfiguration */
        return $this->mapToEntity($response->json('data', $response->json()));
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
     */
    public function incidents(): Builder
    {
        return $this->createBuilderWithEndpoint('/alerts/incidents/stream')
            ->withoutDefaultPageSize()
            ->repeatQueryParameters('configurationIds')
            ->usingEntity(AlertIncident::class);
    }

    /**
     * Create a new query builder for this resource.
     */
    public function query(): Builder
    {
        return (new Builder($this))
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

        /** @var AlertConfiguration */
        return $this->mapToEntity($response->json('data', $response->json()));
    }

    /**
     * {@inheritDoc}
     */
    protected function supportedGenericOperations(): array
    {
        return ['all', 'create'];
    }
}
