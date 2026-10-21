<?php

namespace Samsara\Resources\Dispatch;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Data\Address\Address;
use Samsara\Resources\Concerns\FindsEntities;
use Samsara\Resources\Concerns\CreatesEntities;
use Samsara\Resources\Concerns\DeletesEntities;
use Samsara\Resources\Concerns\UpdatesEntities;

/**
 * Addresses resource for the Samsara API.
 *
 * Provides access to address/geofence management endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @extends \Samsara\Resources\Resource<Address>
 */
class AddressesResource extends Resource
{
    /** @use CreatesEntities<Address> */
    use CreatesEntities;

    /** @use DeletesEntities<Address> */
    use DeletesEntities;

    /** @use FindsEntities<Address> */
    use FindsEntities;

    /** @use UpdatesEntities<Address> */
    use UpdatesEntities;

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
     *
     * @return Builder<Address>
     */
    public function query(): Builder
    {
        return $this->newQuery();
    }
}
