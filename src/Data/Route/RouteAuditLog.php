<?php

namespace Samsara\Data\Route;

use Samsara\Data\Entity;

/**
 * Route audit-log feed result.
 *
 * @property-read array<string, mixed>|null $changes Route changes
 * @property-read string|null $operation Route tracking operation
 * @property-read array<string, mixed>|null $route Route snapshot
 * @property-read string|null $source Change source
 * @property-read string|null $time Change time
 * @property-read string|null $type Feed item type
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class RouteAuditLog extends Entity
{
    /**
     * Get the route snapshot associated with the change.
     */
    public function getRoute(): ?Route
    {
        $route = $this->get('route');

        return is_array($route) ? Route::make($route) : null;
    }
}
