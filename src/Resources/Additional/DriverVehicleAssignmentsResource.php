<?php

namespace Samsara\Resources\Additional;

use Samsara\Data\Entity;
use Samsara\Query\Builder;
use Samsara\Resources\Resource;

/**
 * DriverVehicleAssignments resource for the Samsara API.
 *
 * Provides access to driver-vehicle assignment endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class DriverVehicleAssignmentsResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/fleet/driver-vehicle-assignments';

    /**
     * Delete driver-vehicle assignments using the contract request body.
     *
     * @param  array<string, mixed>  $data
     */
    public function deleteAssignments(array $data): bool
    {
        $response = $this->executeSdkRequest(
            fn () => $this->client()->delete($this->endpoint, $data),
            $this->endpoint,
        );

        return $response->successful();
    }

    /**
     * Create a new query builder for this resource.
     */
    public function query(): Builder
    {
        return $this->createBuilderWithEndpoint($this->endpoint)->withoutDefaultPageSize();
    }

    /**
     * Update a driver-vehicle assignment at the collection endpoint.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateAssignment(array $data): Entity
    {
        $response = $this->executeSdkRequest(
            fn () => $this->client()->patch($this->endpoint, $data),
            $this->endpoint,
        );

        return Entity::make($this->decodeJsonData($response, $this->endpoint, false));
    }

    /**
     * {@inheritDoc}
     */
    protected function supportedGenericOperations(): array
    {
        return ['create'];
    }
}
