<?php

namespace Samsara\Resources\Additional;

use Samsara\Data\Entity;
use Samsara\Query\Builder;
use Samsara\Resources\Resource;

/**
 * DriverTrailerAssignments resource for the Samsara API.
 *
 * Provides access to driver-trailer assignment endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class DriverTrailerAssignmentsResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/driver-trailer-assignments';

    /**
     * Create a new query builder for this resource.
     */
    public function query(): Builder
    {
        return $this->createBuilderWithEndpoint($this->endpoint)->withoutDefaultPageSize();
    }

    /**
     * Update a driver-trailer assignment using its required query ID.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateAssignment(string $id, array $data): Entity
    {
        $requestEndpoint = $this->endpoint.'?id='.rawurlencode($id);
        $response = $this->executeSdkRequest(
            fn () => $this->client()->patch($requestEndpoint, $data),
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
