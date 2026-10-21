<?php

namespace Samsara\Resources\Organization;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Data\Contact\Contact;
use Samsara\Resources\Concerns\FindsEntities;
use Samsara\Resources\Concerns\CreatesEntities;
use Samsara\Resources\Concerns\DeletesEntities;
use Samsara\Resources\Concerns\UpdatesEntities;

/**
 * Contacts resource for the Samsara API.
 *
 * Provides access to contact management endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @extends \Samsara\Resources\Resource<Contact>
 */
class ContactsResource extends Resource
{
    /** @use CreatesEntities<Contact> */
    use CreatesEntities;

    /** @use DeletesEntities<Contact> */
    use DeletesEntities;

    /** @use FindsEntities<Contact> */
    use FindsEntities;

    /** @use UpdatesEntities<Contact> */
    use UpdatesEntities;

    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/contacts';

    /**
     * The entity class for this resource.
     */
    protected string $entity = Contact::class;

    /**
     * Create a new query builder for this resource.
     *
     * @return Builder<Contact>
     */
    public function query(): Builder
    {
        return $this->newQuery();
    }
}
