<?php

namespace Samsara\Resources\Additional;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;

/**
 * Issues resource for the Samsara API.
 *
 * Provides access to vehicle/equipment issue tracking endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class IssuesResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/issues';

    /**
     * Get a query builder for issues stream.
     */
    public function stream(): Builder
    {
        return $this->createBuilderWithEndpoint('/issues/stream');
    }
}
