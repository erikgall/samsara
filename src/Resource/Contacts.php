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
    public function createContact(array $payload = []): Response
    {
        return $this->connector->send(new CreateContact($payload));
    }

    /**
     * @param  string  $id  Unique identifier for the contact.
     */
    public function deleteContact(string $id): Response
    {
        return $this->connector->send(new DeleteContact($id));
    }

    /**
     * @param  string  $id  Unique identifier for the contact.
     */
    public function getContact(string $id): Response
    {
        return $this->connector->send(new GetContact($id));
    }

    /**
     * @param  int  $limit  The limit for how many objects will be in the response. Default and max for this value is 512 objects.
     */
    public function listContacts(?int $limit = null): Response
    {
        return $this->connector->send(new ListContacts($limit));
    }

    /**
     * @param  string  $id  Unique identifier for the contact.
     */
    public function updateContact(string $id, array $payload = []): Response
    {
        return $this->connector->send(new UpdateContact($id, $payload));
    }
}
