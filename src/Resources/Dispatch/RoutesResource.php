<?php

namespace Samsara\Resources\Dispatch;

use Samsara\Query\Builder;
use Samsara\Data\Route\Route;
use Samsara\Resources\Resource;

/**
 * Routes resource for the Samsara API.
 *
 * Provides access to route management endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class RoutesResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/fleet/routes';

    /**
     * The entity class for this resource.
     *
     * @var class-string<Route>
     */
    protected string $entity = Route::class;

    /**
     * Get a query builder for route audit logs.
     */
    public function auditLogs(): Builder
    {
        return $this->createBuilderWithEndpoint('/fleet/routes/audit-logs/feed');
    }

    /**
     * Create a new query builder for this resource.
     */
    public function query(): Builder
    {
        return new Builder($this);
    }
}
