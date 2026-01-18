<?php

namespace Samsara\Resources\Telematics;

use Samsara\Query\Builder;
use Samsara\Data\Trip\Trip;
use Samsara\Resources\Resource;

/**
 * Trips resource for the Samsara API.
 *
 * Provides access to trip stream endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class TripsResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/trips/stream';

    /**
     * The entity class for this resource.
     *
     * @var class-string<Trip>
     */
    protected string $entity = Trip::class;

    /**
     * Get a query builder for completed trips.
     */
    public function completed(): Builder
    {
        return $this->query()->where('completionStatus', 'completed');
    }

    /**
     * Get a query builder for in-progress trips.
     */
    public function inProgress(): Builder
    {
        return $this->query()->where('completionStatus', 'inProgress');
    }

    /**
     * Create a new query builder for this resource.
     */
    public function query(): Builder
    {
        return new Builder($this);
    }
}
