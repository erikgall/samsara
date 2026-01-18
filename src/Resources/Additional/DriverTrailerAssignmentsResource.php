<?php

namespace ErikGall\Samsara\Resources\Additional;

use ErikGall\Samsara\Query\Builder;
use ErikGall\Samsara\Resources\Resource;

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
        return new Builder($this);
    }
}
