<?php

namespace ErikGall\Samsara\Resources\Additional;

use ErikGall\Samsara\Query\Builder;
use ErikGall\Samsara\Resources\Resource;

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
     * Create a new query builder for this resource.
     */
    public function query(): Builder
    {
        return new Builder($this);
    }
}
