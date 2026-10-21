<?php

namespace Samsara\Resources\Telematics;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Data\Vehicle\VehicleLocationResult;
use Samsara\Data\Vehicle\VehicleLocationSeries;

/**
 * Vehicle locations resource for the Samsara API.
 *
 * Provides access to vehicle location endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @extends \Samsara\Resources\Resource<VehicleLocationResult>
 */
class VehicleLocationsResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/fleet/vehicles/locations';

    /**
     * The entity class for this resource.
     */
    protected string $entity = VehicleLocationResult::class;

    /**
     * Get a query builder for current vehicle locations.
     *
     * @return Builder<VehicleLocationResult>
     */
    public function current(): Builder
    {
        return $this->query();
    }

    /**
     * Get a query builder for vehicle locations feed.
     *
     * @return Builder<VehicleLocationSeries>
     */
    public function feed(): Builder
    {
        return $this->newQuery('/fleet/vehicles/locations/feed')
            ->withoutDefaultPageSize()
            ->usingEntity(VehicleLocationSeries::class);
    }

    /**
     * Get a query builder for vehicle locations history.
     *
     * @return Builder<VehicleLocationSeries>
     */
    public function history(): Builder
    {
        return $this->newQuery('/fleet/vehicles/locations/history')
            ->withoutDefaultPageSize()
            ->usingEntity(VehicleLocationSeries::class);
    }

    /**
     * Create a new query builder for this resource.
     *
     * @return Builder<VehicleLocationResult>
     */
    public function query(): Builder
    {
        return $this->newQuery()
            ->withoutDefaultPageSize();
    }
}
