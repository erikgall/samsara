<?php

namespace Samsara\Resources\Additional;

use Samsara\Data\Entity;
use Samsara\Query\Builder;
use Samsara\Resources\Resource;

/**
 * Tachograph resource for the Samsara API (EU Only).
 *
 * Provides access to tachograph data endpoints for EU compliance.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @extends \Samsara\Resources\Resource<Entity>
 */
class TachographResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/fleet/drivers/tachograph-activity/history';

    /**
     * Get a query builder for driver activity history.
     *
     * @return Builder<Entity>
     */
    public function driverActivityHistory(): Builder
    {
        return $this->newQuery('/fleet/drivers/tachograph-activity/history')
            ->withoutDefaultPageSize();
    }

    /**
     * Get a query builder for driver files history.
     *
     * @return Builder<Entity>
     */
    public function driverFilesHistory(): Builder
    {
        return $this->newQuery('/fleet/drivers/tachograph-files/history')
            ->withoutDefaultPageSize();
    }

    /**
     * Get a query builder for vehicle files history.
     *
     * @return Builder<Entity>
     */
    public function vehicleFilesHistory(): Builder
    {
        return $this->newQuery('/fleet/vehicles/tachograph-files/history')
            ->withoutDefaultPageSize();
    }
}
