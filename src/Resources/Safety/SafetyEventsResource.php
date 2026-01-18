<?php

namespace ErikGall\Samsara\Resources\Safety;

use ErikGall\Samsara\Query\Builder;
use ErikGall\Samsara\Resources\Resource;
use ErikGall\Samsara\Data\Safety\SafetyEvent;

/**
 * Safety events resource for the Samsara API.
 *
 * Provides access to safety events and audit logs endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class SafetyEventsResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/fleet/safety-events';

    /**
     * The entity class for this resource.
     *
     * @var class-string<SafetyEvent>
     */
    protected string $entity = SafetyEvent::class;

    /**
     * Get a query builder for safety event audit logs feed.
     */
    public function auditLogs(): Builder
    {
        return $this->createBuilderWithEndpoint('/fleet/safety-events/audit-logs/feed');
    }

    /**
     * Create a new query builder for this resource.
     */
    public function query(): Builder
    {
        return new Builder($this);
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
