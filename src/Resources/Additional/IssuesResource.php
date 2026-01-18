<?php

namespace ErikGall\Samsara\Resources\Additional;

use ErikGall\Samsara\Query\Builder;
use ErikGall\Samsara\Resources\Resource;

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
