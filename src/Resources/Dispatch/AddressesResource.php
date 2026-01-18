<?php

namespace Samsara\Resources\Dispatch;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Data\Address\Address;

/**
 * Addresses resource for the Samsara API.
 *
 * Provides access to address/geofence management endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class AddressesResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/addresses';

    /**
     * The entity class for this resource.
     *
     * @var class-string<Address>
     */
    protected string $entity = Address::class;

    /**
     * Create a new query builder for this resource.
     */
    public function query(): Builder
    {
        return new Builder($this);
    }
}
