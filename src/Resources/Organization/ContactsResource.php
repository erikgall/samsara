<?php

namespace Samsara\Resources\Organization;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Data\Contact\Contact;

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
