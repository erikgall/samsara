<?php

namespace ErikGall\Samsara\Resources\Organization;

use ErikGall\Samsara\Query\Builder;
use ErikGall\Samsara\Data\User\User;
use ErikGall\Samsara\Resources\Resource;

/**
 * Users resource for the Samsara API.
 *
 * Provides access to user management endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class UsersResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/users';

    /**
     * The entity class for this resource.
     *
     * @var class-string<User>
     */
    protected string $entity = User::class;

    /**
     * Create a new query builder for this resource.
     */
    public function query(): Builder
    {
        return new Builder($this);
    }
}
