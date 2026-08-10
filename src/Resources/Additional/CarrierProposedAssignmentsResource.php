<?php

namespace Samsara\Resources\Additional;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;

/**
 * CarrierProposedAssignments resource for the Samsara API.
 *
 * Provides access to carrier proposed driver-vehicle assignments endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class CarrierProposedAssignmentsResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/fleet/carrier-proposed-assignments';

    /**
     * Create a query builder for cursor-aware assignment access.
     */
    public function query(): Builder
    {
        return new Builder($this);
    }

    /**
     * {@inheritDoc}
     */
    protected function supportedGenericOperations(): array
    {
        return ['all', 'create', 'delete'];
    }
}
