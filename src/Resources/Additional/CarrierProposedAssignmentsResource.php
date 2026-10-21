<?php

namespace Samsara\Resources\Additional;

use Samsara\Data\Entity;
use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Resources\Concerns\CreatesEntities;
use Samsara\Resources\Concerns\DeletesEntities;

/**
 * CarrierProposedAssignments resource for the Samsara API.
 *
 * Provides access to carrier proposed driver-vehicle assignments endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @extends \Samsara\Resources\Resource<Entity>
 */
class CarrierProposedAssignmentsResource extends Resource
{
    /** @use CreatesEntities<Entity> */
    use CreatesEntities;

    /** @use DeletesEntities<Entity> */
    use DeletesEntities;

    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/fleet/carrier-proposed-assignments';

    /**
     * Create a query builder for cursor-aware assignment access.
     *
     * @return Builder<Entity>
     */
    public function query(): Builder
    {
        return $this->newQuery();
    }
}
