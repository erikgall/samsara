<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\Contacts\GetContact;
use ErikGall\Samsara\Requests\Contacts\ListContacts;
use ErikGall\Samsara\Requests\Contacts\CreateContact;
use ErikGall\Samsara\Requests\Contacts\DeleteContact;
use ErikGall\Samsara\Requests\Contacts\UpdateContact;

class Contacts extends Resource
{
    /**
     * Create a new Contact resource.
     *
     * @param  array  $payload  The data to create the contact.
     * @return Response
     */
    public function create(array $payload = []): Response
    {
        return $this->connector->send(new CreateContact($payload));
    }

    /**
     * Delete a Contact resource.
     *
     * @param  string  $id  Unique identifier for the contact.
     * @return Response
     */
    public function delete(string $id): Response
    {
        return $this->connector->send(new DeleteContact($id));
    }

    /**
     * Find a Contact resource by ID.
     *
     * @param  string  $id  Unique identifier for the contact.
     * @return Response
     */
    public function find(string $id): Response
    {
        return $this->connector->send(new GetContact($id));
    }

    /**
     * Get a list of Contact resources.
     *
     * @param  int|null  $limit  The limit for how many objects will be in the response. Default and max for this value is 512 objects.
     * @return Response
     */
    public function get(?int $limit = null): Response
    {
        return $this->connector->send(new ListContacts($limit));
    }

    /**
     * Update a Contact resource.
     *
     * @param  string  $id  Unique identifier for the contact.
     * @param  array  $payload  The data to update the contact.
     * @return Response
     */
    public function update(string $id, array $payload = []): Response
    {
        return $this->connector->send(new UpdateContact($id, $payload));
    }
}
