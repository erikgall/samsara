<?php

namespace Samsara\Resources\Dispatch;

use Samsara\Query\Builder;
use Samsara\Data\Route\Route;
use Samsara\Resources\Resource;
use Samsara\Data\Route\RouteAuditLog;
use Samsara\Resources\Concerns\FindsEntities;
use Samsara\Resources\Concerns\CreatesEntities;
use Samsara\Resources\Concerns\DeletesEntities;
use Samsara\Resources\Concerns\UpdatesEntities;

/**
 * Routes resource for the Samsara API.
 *
 * Provides access to route management endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @extends \Samsara\Resources\Resource<Route>
 */
class RoutesResource extends Resource
{
    /** @use CreatesEntities<Route> */
    use CreatesEntities;

    /** @use DeletesEntities<Route> */
    use DeletesEntities;

    /** @use FindsEntities<Route> */
    use FindsEntities;

    /** @use UpdatesEntities<Route> */
    use UpdatesEntities;

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
     *
     * @return Builder<RouteAuditLog>
     */
    public function auditLogs(): Builder
    {
        return $this->newQuery('/fleet/routes/audit-logs/feed')
            ->withoutDefaultPageSize()
            ->usingEntity(RouteAuditLog::class);
    }

    /**
     * Create a new query builder for this resource.
     *
     * @return Builder<Route>
     */
    public function query(): Builder
    {
        return $this->newQuery();
    }
}
