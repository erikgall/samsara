<?php

namespace Samsara\Resources\Additional;

use Samsara\Data\Entity;
use Samsara\Query\Builder;
use Samsara\Resources\Resource;

/**
 * Speeding resource for the Samsara API.
 *
 * Provides access to speeding interval stream endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @extends \Samsara\Resources\Resource<Entity>
 */
class SpeedingResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/speeding-intervals/stream';

    /**
     * Get a query builder for speeding intervals stream.
     *
     * @return Builder<Entity>
     */
    public function intervalsStream(): Builder
    {
        return $this->newQuery()->withoutDefaultPageSize();
    }
}
