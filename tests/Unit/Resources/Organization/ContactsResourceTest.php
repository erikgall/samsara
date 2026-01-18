<?php

namespace Samsara\Tests\Unit\Resources\Organization;

use Samsara\Samsara;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use Samsara\Data\Contact\Contact;
use Samsara\Data\EntityCollection;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Resources\Organization\ContactsResource;

/**
 * Unit tests for the ContactsResource.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class ContactsResourceTest extends TestCase
{
    protected HttpFactory $http;

    protected Samsara $samsara;

    protected function setUp(): void
    {
        parent::setUp();

        $this->http = new HttpFactory;
        $this->samsara = new Samsara('test-token');
        $this->samsara->setHttpFactory($this->http);
    }

    #[Test]
    public function it_can_be_created(): void
    {
        $resource = new ContactsResource($this->samsara);

        $this->assertInstanceOf(ContactsResource::class, $resource);
    }

    #[Test]
    public function it_can_create_contact(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    'id'        => 'contact-1',
                    'firstName' => 'John',
                    'lastName'  => 'Doe',
                    'email'     => 'john@example.com',
                ],
            ]),
        ]);

        $resource = new ContactsResource($this->samsara);
        $contact = $resource->create([
            'firstName' => 'John',
            'lastName'  => 'Doe',
            'email'     => 'john@example.com',
        ]);

        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertSame('John', $contact->firstName);
    }

    #[Test]
    public function it_can_delete_contact(): void
    {
        $this->http->fake([
            '*' => $this->http->response([], 204),
        ]);

        $resource = new ContactsResource($this->samsara);
        $result = $resource->delete('contact-1');

        $this->assertTrue($result);
    }

    #[Test]
    public function it_can_find_contact_by_id(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    'id'        => 'contact-1',
                    'firstName' => 'Jane',
                    'lastName'  => 'Doe',
                ],
            ]),
        ]);

        $resource = new ContactsResource($this->samsara);
        $contact = $resource->find('contact-1');

        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertSame('Jane', $contact->firstName);
    }

    #[Test]
    public function it_can_get_all_contacts(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    [
                        'id'        => 'contact-1',
                        'firstName' => 'John',
                    ],
                    [
                        'id'        => 'contact-2',
                        'firstName' => 'Jane',
                    ],
                ],
            ]),
        ]);

        $resource = new ContactsResource($this->samsara);
        $contacts = $resource->all();

        $this->assertInstanceOf(EntityCollection::class, $contacts);
        $this->assertCount(2, $contacts);
        $this->assertInstanceOf(Contact::class, $contacts->first());
    }

    #[Test]
    public function it_can_return_query_builder(): void
    {
        $resource = new ContactsResource($this->samsara);
        $query = $resource->query();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new ContactsResource($this->samsara);

        $this->assertSame('/contacts', $resource->getEndpoint());
    }

    #[Test]
    public function it_has_correct_entity_class(): void
    {
        $resource = new ContactsResource($this->samsara);

        $this->assertSame(Contact::class, $resource->getEntityClass());
    }
}
