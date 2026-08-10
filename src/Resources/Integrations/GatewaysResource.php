<?php

namespace Samsara\Resources\Integrations;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Data\Vehicle\Gateway;

/**
 * Gateways resource for the Samsara API.
 *
 * Provides access to gateway device management endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class GatewaysResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/gateways';

    /**
     * The entity class for this resource.
     *
     * @var class-string<Gateway>
     */
    protected string $entity = Gateway::class;

    /**
     * Create a query builder for cursor-paginated gateway results.
     */
    public function query(): Builder
    {
        return (new Builder($this))
            ->withoutDefaultPageSize()
            ->usingEntity(Gateway::class);
    }

    /**
     * Get the inherited generic operations supported by this resource.
     *
     * @return array<int, string>
     */
    protected function supportedGenericOperations(): array
    {
        return ['all', 'create', 'delete'];
    }
}
