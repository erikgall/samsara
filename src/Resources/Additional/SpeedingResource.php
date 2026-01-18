<?php

namespace Samsara\Resources\Additional;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;

/**
 * Speeding resource for the Samsara API.
 *
 * Provides access to speeding interval stream endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class SpeedingResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/speeding-intervals/stream';

    /**
     * Get a query builder for speeding intervals stream.
     */
    public function intervalsStream(): Builder
    {
        return new Builder($this);
    }
}
