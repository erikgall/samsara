<?php

namespace Samsara\Resources\Additional;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;

/**
 * Tachograph resource for the Samsara API (EU Only).
 *
 * Provides access to tachograph data endpoints for EU compliance.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class TachographResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/tachograph';

    /**
     * Get a query builder for driver activity history.
     */
    public function driverActivityHistory(): Builder
    {
        return $this->createBuilderWithEndpoint('/tachograph/driver-activity-history');
    }

    /**
     * Get a query builder for driver files history.
     */
    public function driverFilesHistory(): Builder
    {
        return $this->createBuilderWithEndpoint('/tachograph/driver-files/history');
    }

    /**
     * Get a query builder for vehicle files history.
     */
    public function vehicleFilesHistory(): Builder
    {
        return $this->createBuilderWithEndpoint('/tachograph/vehicle-files/history');
    }
}
