<?php

namespace Samsara\Resources\Additional;

use Samsara\Data\Entity;
use Samsara\Query\Builder;
use Samsara\Resources\Resource;

/**
 * RouteEvents resource for the Samsara API.
 *
 * Provides access to route event endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @extends \Samsara\Resources\Resource<Entity>
 */
class RouteEventsResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/route-events/stream';

    /**
     * The entity class for this resource.
     */
    protected string $entity = Entity::class;

    /**
     * Create a new query builder for this resource.
     *
     * @return Builder<Entity>
     */
    public function query(): Builder
    {
        return $this->newQuery();
    }
}
