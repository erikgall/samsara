<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\Users\GetUser;
use ErikGall\Samsara\Requests\Users\ListUsers;
use ErikGall\Samsara\Requests\Users\CreateUser;
use ErikGall\Samsara\Requests\Users\DeleteUser;
use ErikGall\Samsara\Requests\Users\UpdateUser;
use ErikGall\Samsara\Requests\Users\ListUserRoles;

class Users extends Resource
{
    /**
     * Create a new User resource.
     *
     * @param  array  $payload  The data to create the user.
     * @return Response
     */
    public function create(array $payload = []): Response
    {
        return $this->connector->send(new CreateUser($payload));
    }

    /**
     * Delete a User resource.
     *
     * @param  string  $id  Unique identifier for the user.
     * @return Response
     */
    public function delete(string $id): Response
    {
        return $this->connector->send(new DeleteUser($id));
    }

    /**
     * Find a User resource by ID.
     *
     * @param  string  $id  Unique identifier for the user.
     * @return Response
     */
    public function find(string $id): Response
    {
        return $this->connector->send(new GetUser($id));
    }

    /**
     * Get a list of User resources.
     *
     * @param  int|null  $limit  The limit for how many objects will be in the response.
     * @return Response
     */
    public function get(?int $limit = null): Response
    {
        return $this->connector->send(new ListUsers($limit));
    }

    /**
     * Get a list of User roles.
     *
     * @param  int|null  $limit  The limit for how many objects will be in the response.
     * @return Response
     */
    public function getRoles(?int $limit = null): Response
    {
        return $this->connector->send(new ListUserRoles($limit));
    }

    /**
     * Update a User resource.
     *
     * @param  string  $id  Unique identifier for the user.
     * @param  array  $payload  The data to update the user.
     * @return Response
     */
    public function update(string $id, array $payload = []): Response
    {
        return $this->connector->send(new UpdateUser($id, $payload));
    }
}
