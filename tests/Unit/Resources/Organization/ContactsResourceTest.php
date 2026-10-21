<?php

namespace Samsara\Tests\Unit\Resources\Organization;

use Samsara\Samsara;
use Samsara\Tests\TestCase;
use Samsara\Data\Contact\Contact;
use Samsara\Data\EntityCollection;
use Illuminate\Http\Client\Request;
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
        $this->http->preventStrayRequests();
        $this->samsara = new Samsara('test-token');
        $this->samsara->setHttpFactory($this->http);
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

    #[Test]
    public function it_queries_contacts(): void
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
        $contacts = $resource->query()->get();

        $this->assertInstanceOf(EntityCollection::class, $contacts);
        $this->assertCount(2, $contacts);
        $this->assertInstanceOf(Contact::class, $contacts->first());
    }

    #[Test]
    public function it_terminal_executes_the_current_contact_list_query(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data'       => [['id' => 'contact-1', 'firstName' => 'Ada']],
                'pagination' => ['endCursor' => '', 'hasNextPage' => false],
            ]),
        ]);

        $page = (new ContactsResource($this->samsara))
            ->query()
            ->after('contact-cursor')
            ->paginate(50);

        $this->assertInstanceOf(Contact::class, $page->items()->first());
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/contacts'
            && $request->data() === ['limit' => 50, 'after' => 'contact-cursor']
        );
    }
}
