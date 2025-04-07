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
    public function createUser(): Response
    {
        return $this->connector->send(new CreateUser);
    }

    /**
     * @param  string  $id  Unique identifier for the user.
     */
    public function deleteUser(string $id): Response
    {
        return $this->connector->send(new DeleteUser($id));
    }

    /**
     * @param  string  $id  Unique identifier for the user.
     */
    public function getUser(string $id): Response
    {
        return $this->connector->send(new GetUser($id));
    }

    /**
     * @param  int  $limit  The limit for how many objects will be in the response. Default and max for this value is 512 objects.
     */
    public function listUserRoles(?int $limit = null): Response
    {
        return $this->connector->send(new ListUserRoles($limit));
    }

    /**
     * @param  int  $limit  The limit for how many objects will be in the response. Default and max for this value is 512 objects.
     */
    public function listUsers(?int $limit = null): Response
    {
        return $this->connector->send(new ListUsers($limit));
    }

    /**
     * @param  string  $id  Unique identifier for the user.
     */
    public function updateUser(string $id): Response
    {
        return $this->connector->send(new UpdateUser($id));
    }
}
