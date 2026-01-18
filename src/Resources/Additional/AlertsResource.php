<?php

namespace ErikGall\Samsara\Resources\Additional;

use ErikGall\Samsara\Query\Builder;
use ErikGall\Samsara\Resources\Resource;
use ErikGall\Samsara\Data\Alert\AlertConfiguration;

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
        $response = $this->client()->post('/alerts/configurations', $data);

        $this->handleError($response);

        /** @var AlertConfiguration */
        return $this->mapToEntity($response->json('data', $response->json()));
    }

    /**
     * Delete alert configurations.
     *
     * @param  array<int, string>  $ids
     */
    public function deleteConfigurations(array $ids): bool
    {
        $response = $this->client()->delete('/alerts/configurations', ['ids' => $ids]);

        $this->handleError($response);

        return $response->successful();
    }

    /**
     * Get a query builder for alert incidents.
     */
    public function incidents(): Builder
    {
        return $this->createBuilderWithEndpoint('/alerts/incidents');
    }

    /**
     * Create a new query builder for this resource.
     */
    public function query(): Builder
    {
        return new Builder($this);
    }

    /**
     * Update an alert configuration.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateConfiguration(string $id, array $data): AlertConfiguration
    {
        $response = $this->client()->patch("/alerts/configurations/{$id}", $data);

        $this->handleError($response);

        /** @var AlertConfiguration */
        return $this->mapToEntity($response->json('data', $response->json()));
    }

    /**
     * Create a builder with a custom endpoint.
     */
    protected function createBuilderWithEndpoint(string $endpoint): Builder
    {
        $originalEndpoint = $this->endpoint;
        $this->endpoint = $endpoint;
        $builder = new Builder($this);
        $this->endpoint = $originalEndpoint;

        return $builder;
    }
}
