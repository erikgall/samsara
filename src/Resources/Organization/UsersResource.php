<?php

namespace Samsara\Resources\Organization;

use Samsara\Query\Builder;
use Samsara\Data\User\User;
use Samsara\Resources\Resource;

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
        return $this->createBuilderWithEndpoint($this->endpoint);
    }

    /**
     * Get the inherited generic operations supported by this resource.
     *
     * @return array<int, string>
     */
    protected function supportedGenericOperations(): array
    {
        return ['all', 'find', 'create', 'update', 'delete'];
    }
}
