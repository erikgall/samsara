<?php

namespace Samsara\Resources\Telematics;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Data\Vehicle\VehicleStats;
use Samsara\Data\Vehicle\VehicleStatsSeries;

/**
 * Vehicle stats resource for the Samsara API.
 *
 * Provides access to vehicle telemetry and stats endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @extends \Samsara\Resources\Resource<VehicleStats>
 */
class VehicleStatsResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/fleet/vehicles/stats';

    /**
     * The entity class for this resource.
     */
    protected string $entity = VehicleStats::class;

    /**
     * Get a query builder for current vehicle stats.
     *
     * @return Builder<VehicleStats>
     */
    public function current(): Builder
    {
        return $this->query();
    }

    /**
     * Get a query builder for engine states stats.
     *
     * @return Builder<VehicleStats>
     */
    public function engineStates(): Builder
    {
        return $this->query()->types(['engineStates']);
    }

    /**
     * Get a query builder for vehicle stats feed.
     *
     * @return Builder<VehicleStatsSeries>
     */
    public function feed(): Builder
    {
        return $this->newQuery('/fleet/vehicles/stats/feed')
            ->withoutDefaultPageSize()
            ->usingEntity(VehicleStatsSeries::class);
    }

    /**
     * Get a query builder for fuel percents stats.
     *
     * @return Builder<VehicleStats>
     */
    public function fuelPercents(): Builder
    {
        return $this->query()->types(['fuelPercents']);
    }

    /**
     * Get a query builder for GPS stats.
     *
     * @return Builder<VehicleStats>
     */
    public function gps(): Builder
    {
        return $this->query()->types(['gps']);
    }

    /**
     * Get a query builder for vehicle stats history.
     *
     * @return Builder<VehicleStatsSeries>
     */
    public function history(): Builder
    {
        return $this->newQuery('/fleet/vehicles/stats/history')
            ->withoutDefaultPageSize()
            ->usingEntity(VehicleStatsSeries::class);
    }

    /**
     * Get a query builder for odometer stats.
     *
     * @return Builder<VehicleStats>
     */
    public function odometer(): Builder
    {
        return $this->query()->types(['obdOdometerMeters', 'gpsOdometerMeters']);
    }

    /**
     * Create a new query builder for this resource.
     *
     * @return Builder<VehicleStats>
     */
    public function query(): Builder
    {
        return $this->newQuery()
            ->withoutDefaultPageSize();
    }
}
