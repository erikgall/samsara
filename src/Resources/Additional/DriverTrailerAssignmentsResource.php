<?php

namespace Samsara\Resources\Additional;

use Samsara\Data\Entity;
use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Resources\Concerns\CreatesEntities;

/**
 * DriverTrailerAssignments resource for the Samsara API.
 *
 * Provides access to driver-trailer assignment endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @extends \Samsara\Resources\Resource<Entity>
 */
class DriverTrailerAssignmentsResource extends Resource
{
    /** @use CreatesEntities<Entity> */
    use CreatesEntities;

    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/driver-trailer-assignments';

    /**
     * Create a new query builder for this resource.
     *
     * @return Builder<Entity>
     */
    public function query(): Builder
    {
        return $this->newQuery()->withoutDefaultPageSize();
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
}
