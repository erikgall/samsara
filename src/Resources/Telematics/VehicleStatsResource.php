<?php

namespace Samsara\Resources\Telematics;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Data\Vehicle\VehicleStats;

/**
 * Vehicle stats resource for the Samsara API.
 *
 * Provides access to vehicle telemetry and stats endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class VehicleStatsResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/fleet/vehicles/stats';

    /**
     * The entity class for this resource.
     *
     * @var class-string<VehicleStats>
     */
    protected string $entity = VehicleStats::class;

    /**
     * Get a query builder for current vehicle stats.
     */
    public function current(): Builder
    {
        return $this->query();
    }

    /**
     * Get a query builder for engine states stats.
     */
    public function engineStates(): Builder
    {
        return $this->query()->types(['engineStates']);
    }

    /**
     * Get a query builder for vehicle stats feed.
     */
    public function feed(): Builder
    {
        return $this->createBuilderWithEndpoint('/fleet/vehicles/stats/feed');
    }

    /**
     * Get a query builder for fuel percents stats.
     */
    public function fuelPercents(): Builder
    {
        return $this->query()->types(['fuelPercents']);
    }

    /**
     * Get a query builder for GPS stats.
     */
    public function gps(): Builder
    {
        return $this->query()->types(['gps']);
    }

    /**
     * Get a query builder for vehicle stats history.
     */
    public function history(): Builder
    {
        return $this->createBuilderWithEndpoint('/fleet/vehicles/stats/history');
    }

    /**
     * Get a query builder for odometer stats.
     */
    public function odometer(): Builder
    {
        return $this->query()->types(['obdOdometerMeters', 'gpsOdometerMeters']);
    }

    /**
     * Create a new query builder for this resource.
     */
    public function query(): Builder
    {
        return new Builder($this);
    }
}
