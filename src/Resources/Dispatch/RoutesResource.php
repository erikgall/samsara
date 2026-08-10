<?php

namespace Samsara\Resources\Dispatch;

use Samsara\Query\Builder;
use Samsara\Data\Route\Route;
use Samsara\Resources\Resource;
use Samsara\Data\Route\RouteAuditLog;

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
        return $this->createBuilderWithEndpoint('/fleet/routes/audit-logs/feed')
            ->withoutDefaultPageSize()
            ->usingEntity(RouteAuditLog::class);
    }

    /**
     * Create a new query builder for this resource.
     */
    public function query(): Builder
    {
        return new Builder($this);
    }

    /**
     * Get the inherited generic operations supported by this resource.
     *
     * @return array<int, string>
     */
    protected function supportedGenericOperations(): array
    {
        return ['find', 'create', 'update', 'delete'];
    }
}
