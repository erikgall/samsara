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
    protected string $endpoint = '/fleet/drivers/tachograph-activity/history';

    /**
     * Get a query builder for driver activity history.
     */
    public function driverActivityHistory(): Builder
    {
        return $this->createBuilderWithEndpoint('/fleet/drivers/tachograph-activity/history')
            ->withoutDefaultPageSize();
    }

    /**
     * Get a query builder for driver files history.
     */
    public function driverFilesHistory(): Builder
    {
        return $this->createBuilderWithEndpoint('/fleet/drivers/tachograph-files/history')
            ->withoutDefaultPageSize();
    }

    /**
     * Get a query builder for vehicle files history.
     */
    public function vehicleFilesHistory(): Builder
    {
        return $this->createBuilderWithEndpoint('/fleet/vehicles/tachograph-files/history')
            ->withoutDefaultPageSize();
    }

    /**
     * {@inheritDoc}
     */
    protected function supportedGenericOperations(): array
    {
        return [];
    }
}
