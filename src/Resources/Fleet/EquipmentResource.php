<?php

namespace ErikGall\Samsara\Resources\Fleet;

use ErikGall\Samsara\Query\Builder;
use ErikGall\Samsara\Resources\Resource;
use ErikGall\Samsara\Data\Equipment\Equipment;

/**
 * Equipment resource for the Samsara API.
 *
 * Provides access to equipment-related endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class EquipmentResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/fleet/equipment';

    /**
     * The entity class for this resource.
     *
     * @var class-string<Equipment>
     */
    protected string $entity = Equipment::class;

    /**
     * Find equipment by external ID.
     *
     * @param  string  $key  The external ID key
     * @param  string  $value  The external ID value
     */
    public function findByExternalId(string $key, string $value): ?Equipment
    {
        $result = $this->query()
            ->where("externalIds[{$key}]", $value)
            ->first();

        if ($result === null) {
            return null;
        }

        /** @var Equipment */
        return $result;
    }

    /**
     * Get a query builder for equipment locations.
     */
    public function locations(): Builder
    {
        return $this->createBuilderWithEndpoint('/fleet/equipment/locations');
    }

    /**
     * Get a query builder for equipment locations feed.
     */
    public function locationsFeed(): Builder
    {
        return $this->createBuilderWithEndpoint('/fleet/equipment/locations/feed');
    }

    /**
     * Get a query builder for equipment locations history.
     */
    public function locationsHistory(): Builder
    {
        return $this->createBuilderWithEndpoint('/fleet/equipment/locations/history');
    }

    /**
     * Create a new query builder for this resource.
     */
    public function query(): Builder
    {
        return new Builder($this);
    }

    /**
     * Get a query builder for equipment stats.
     */
    public function stats(): Builder
    {
        return $this->createBuilderWithEndpoint('/fleet/equipment/stats');
    }

    /**
     * Get a query builder for equipment stats feed.
     */
    public function statsFeed(): Builder
    {
        return $this->createBuilderWithEndpoint('/fleet/equipment/stats/feed');
    }

    /**
     * Get a query builder for equipment stats history.
     */
    public function statsHistory(): Builder
    {
        return $this->createBuilderWithEndpoint('/fleet/equipment/stats/history');
    }

    /**
     * Create a builder with a custom endpoint.
     */
    protected function createBuilderWithEndpoint(string $endpoint): Builder
    {
        $originalEndpoint = $this->endpoint;
        $this->endpoint = $endpoint;
        $builder = new Builder($this);
        $this->endpoint = $originalEndpoint;

        return $builder;
    }
}
