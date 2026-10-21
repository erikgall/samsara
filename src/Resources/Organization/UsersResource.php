<?php

namespace Samsara\Resources\Organization;

use Samsara\Query\Builder;
use Samsara\Data\User\User;
use Samsara\Resources\Resource;
use Samsara\Resources\Concerns\FindsEntities;
use Samsara\Resources\Concerns\CreatesEntities;
use Samsara\Resources\Concerns\DeletesEntities;
use Samsara\Resources\Concerns\UpdatesEntities;

/**
 * Users resource for the Samsara API.
 *
 * Provides access to user management endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @extends \Samsara\Resources\Resource<User>
 */
class UsersResource extends Resource
{
    /** @use CreatesEntities<User> */
    use CreatesEntities;

    /** @use DeletesEntities<User> */
    use DeletesEntities;

    /** @use FindsEntities<User> */
    use FindsEntities;

    /** @use UpdatesEntities<User> */
    use UpdatesEntities;

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
     *
     * @return Builder<User>
     */
    public function query(): Builder
    {
        return $this->newQuery();
    }
}
