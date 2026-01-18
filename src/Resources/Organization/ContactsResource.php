<?php

namespace ErikGall\Samsara\Resources\Organization;

use ErikGall\Samsara\Query\Builder;
use ErikGall\Samsara\Resources\Resource;
use ErikGall\Samsara\Data\Contact\Contact;

/**
 * Contacts resource for the Samsara API.
 *
 * Provides access to contact management endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class ContactsResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/contacts';

    /**
     * The entity class for this resource.
     *
     * @var class-string<Contact>
     */
    protected string $entity = Contact::class;

    /**
     * Create a new query builder for this resource.
     */
    public function query(): Builder
    {
        return new Builder($this);
    }
}
