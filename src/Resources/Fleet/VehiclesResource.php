<?php

namespace Samsara\Resources\Fleet;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Data\Vehicle\Vehicle;

/**
 * Vehicles resource for the Samsara API.
 *
 * Provides access to vehicle-related endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class VehiclesResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/fleet/vehicles';

    /**
     * The entity class for this resource.
     *
     * @var class-string<Vehicle>
     */
    protected string $entity = Vehicle::class;

    /**
     * Find a vehicle by external ID.
     *
     * @param  string  $key  The external ID key
     * @param  string  $value  The external ID value
     */
    public function findByExternalId(string $key, string $value): ?Vehicle
    {
        $result = $this->query()
            ->where("externalIds[{$key}]", $value)
            ->first();

        if ($result === null) {
            return null;
        }

        /** @var Vehicle */
        return $result;
    }

    /**
     * Create a new query builder for this resource.
     */
    public function query(): Builder
    {
        return new Builder($this);
    }
}
