<?php

namespace ErikGall\Samsara\Resources\Additional;

use ErikGall\Samsara\Query\Builder;
use ErikGall\Samsara\Resources\Resource;

/**
 * Idling resource for the Samsara API.
 *
 * Provides access to vehicle idling events endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class IdlingResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/idling/events';

    /**
     * Get a query builder for idling events.
     */
    public function events(): Builder
    {
        return new Builder($this);
    }
}
