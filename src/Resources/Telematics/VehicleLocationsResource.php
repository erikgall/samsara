<?php

namespace Samsara\Resources\Telematics;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;

/**
 * Vehicle locations resource for the Samsara API.
 *
 * Provides access to vehicle location endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class VehicleLocationsResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/fleet/vehicles/locations';

    /**
     * Get a query builder for current vehicle locations.
     */
    public function current(): Builder
    {
        return $this->query();
    }

    /**
     * Get a query builder for vehicle locations feed.
     */
    public function feed(): Builder
    {
        return $this->createBuilderWithEndpoint('/fleet/vehicles/locations/feed');
    }

    /**
     * Get a query builder for vehicle locations history.
     */
    public function history(): Builder
    {
        return $this->createBuilderWithEndpoint('/fleet/vehicles/locations/history');
    }

    /**
     * Create a new query builder for this resource.
     */
    public function query(): Builder
    {
        return new Builder($this);
    }
}
