<?php

namespace Samsara\Resources\Safety;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Data\Safety\SafetyEvent;
use Samsara\Data\Safety\SafetyEventActivity;

/**
 * Safety events resource for the Samsara API.
 *
 * Provides access to safety events and audit logs endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @extends \Samsara\Resources\Resource<SafetyEvent>
 */
class SafetyEventsResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/fleet/safety-events';

    /**
     * The entity class for this resource.
     */
    protected string $entity = SafetyEvent::class;

    /**
     * Get a query builder for safety event audit logs feed.
     *
     * @return Builder<SafetyEventActivity>
     */
    public function auditLogs(): Builder
    {
        return $this->newQuery('/fleet/safety-events/audit-logs/feed')
            ->withoutDefaultPageSize()
            ->usingEntity(SafetyEventActivity::class);
    }

    /**
     * Create a new query builder for this resource.
     *
     * @return Builder<SafetyEvent>
     */
    public function query(): Builder
    {
        return $this->newQuery()
            ->withoutDefaultPageSize();
    }
}
