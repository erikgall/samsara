<?php

namespace Samsara\Resources\Fleet;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Data\Equipment\Equipment;
use Samsara\Data\Equipment\EquipmentStats;
use Samsara\Resources\Concerns\FindsEntities;
use Samsara\Data\Equipment\EquipmentStatsSeries;
use Samsara\Data\Equipment\EquipmentLocationResult;
use Samsara\Data\Equipment\EquipmentLocationSeries;
use Samsara\Exceptions\UnsupportedOperationException;

/**
 * Equipment resource for the Samsara API.
 *
 * Provides access to equipment-related endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @extends \Samsara\Resources\Resource<Equipment>
 */
class EquipmentResource extends Resource
{
    /** @use FindsEntities<Equipment> */
    use FindsEntities;

    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/fleet/equipment';

    /**
     * The entity class for this resource.
     */
    protected string $entity = Equipment::class;

    /**
     * External-ID equipment lookup is unsupported; use find() with a Samsara equipment ID.
     *
     * @param  string  $key  The external ID key
     * @param  string  $value  The external ID value
     *
     * @throws UnsupportedOperationException Always.
     */
    public function findByExternalId(string $key, string $value): ?Equipment
    {
        throw new UnsupportedOperationException(
            'Equipment cannot be looked up by external ID in the current API contract. Use find() with a Samsara equipment ID.',
            0,
            null,
            ['key' => $key, 'value' => $value]
        );
    }

    /**
     * Get a query builder for equipment locations.
     *
     * @return Builder<EquipmentLocationResult>
     */
    public function locations(): Builder
    {
        return $this->newQuery('/fleet/equipment/locations')
            ->withoutDefaultPageSize()
            ->usingEntity(EquipmentLocationResult::class);
    }

    /**
     * Get a query builder for equipment locations feed.
     *
     * @return Builder<EquipmentLocationSeries>
     */
    public function locationsFeed(): Builder
    {
        return $this->newQuery('/fleet/equipment/locations/feed')
            ->withoutDefaultPageSize()
            ->usingEntity(EquipmentLocationSeries::class);
    }

    /**
     * Get a query builder for equipment locations history.
     *
     * @return Builder<EquipmentLocationSeries>
     */
    public function locationsHistory(): Builder
    {
        return $this->newQuery('/fleet/equipment/locations/history')
            ->withoutDefaultPageSize()
            ->usingEntity(EquipmentLocationSeries::class);
    }

    /**
     * Create a new query builder for this resource.
     *
     * @return Builder<Equipment>
     */
    public function query(): Builder
    {
        return $this->newQuery();
    }

    /**
     * Get a query builder for equipment stats.
     *
     * @return Builder<EquipmentStats>
     */
    public function stats(): Builder
    {
        return $this->newQuery('/fleet/equipment/stats')
            ->withoutDefaultPageSize()
            ->usingEntity(EquipmentStats::class);
    }

    /**
     * Get a query builder for equipment stats feed.
     *
     * @return Builder<EquipmentStatsSeries>
     */
    public function statsFeed(): Builder
    {
        return $this->newQuery('/fleet/equipment/stats/feed')
            ->withoutDefaultPageSize()
            ->usingEntity(EquipmentStatsSeries::class);
    }

    /**
     * Get a query builder for equipment stats history.
     *
     * @return Builder<EquipmentStatsSeries>
     */
    public function statsHistory(): Builder
    {
        return $this->newQuery('/fleet/equipment/stats/history')
            ->withoutDefaultPageSize()
            ->usingEntity(EquipmentStatsSeries::class);
    }
}
