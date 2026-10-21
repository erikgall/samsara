<?php

namespace Samsara\Resources\Integrations;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Data\Vehicle\Gateway;
use Samsara\Resources\Concerns\CreatesEntities;
use Samsara\Resources\Concerns\DeletesEntities;

/**
 * Gateways resource for the Samsara API.
 *
 * Provides access to gateway device management endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @extends \Samsara\Resources\Resource<Gateway>
 */
class GatewaysResource extends Resource
{
    /** @use CreatesEntities<Gateway> */
    use CreatesEntities;

    /** @use DeletesEntities<Gateway> */
    use DeletesEntities;

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
     *
     * @return Builder<Gateway>
     */
    public function query(): Builder
    {
        return $this->newQuery()
            ->withoutDefaultPageSize();
    }
}
