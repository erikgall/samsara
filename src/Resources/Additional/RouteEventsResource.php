<?php

namespace ErikGall\Samsara\Resources\Additional;

use ErikGall\Samsara\Data\Entity;
use ErikGall\Samsara\Query\Builder;
use ErikGall\Samsara\Resources\Resource;

/**
 * RouteEvents resource for the Samsara API.
 *
 * Provides access to route event endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class RouteEventsResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/route-events';

    /**
     * The entity class for this resource.
     *
     * @var class-string<Entity>
     */
    protected string $entity = Entity::class;

    /**
     * Create a new query builder for this resource.
     */
    public function query(): Builder
    {
        return new Builder($this);
    }
}
