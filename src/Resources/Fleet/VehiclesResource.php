<?php

namespace Samsara\Resources\Fleet;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Data\Vehicle\Vehicle;
use Samsara\Resources\Concerns\FindsEntities;
use Samsara\Resources\Concerns\UpdatesEntities;

/**
 * Vehicles resource for the Samsara API.
 *
 * Provides access to vehicle-related endpoints.
 *
 * Note: Vehicles are automatically created when a Samsara Vehicle Gateway is installed.
 * To manually create vehicles, use the Assets API (POST /assets with type: "vehicle").
 * Vehicles cannot be deleted; they can only be marked as retired via the update method.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @extends \Samsara\Resources\Resource<Vehicle>
 */
class VehiclesResource extends Resource
{
    /** @use FindsEntities<Vehicle> */
    use FindsEntities;

    /** @use UpdatesEntities<Vehicle> */
    use UpdatesEntities;

    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/fleet/vehicles';

    /**
     * The entity class for this resource.
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
        return $this->findByEncodedPathSegment(rawurlencode($key).':'.rawurlencode($value));
    }

    /**
     * Create a new query builder for this resource.
     *
     * @return Builder<Vehicle>
     */
    public function query(): Builder
    {
        return $this->newQuery()
            ->repeatQueryParameters('attributes');
    }
}
