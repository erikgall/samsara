<?php

namespace Samsara\Tests\Unit\Resources\Organization;

use Samsara\Samsara;
use Samsara\Query\Builder;
use Samsara\Data\User\User;
use Samsara\Tests\TestCase;
use Samsara\Data\EntityCollection;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Resources\Organization\UsersResource;
use Illuminate\Http\Client\Factory as HttpFactory;

/**
 * Unit tests for the UsersResource.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class UsersResourceTest extends TestCase
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
        $resource = new UsersResource($this->samsara);

        $this->assertInstanceOf(UsersResource::class, $resource);
    }

    #[Test]
    public function it_can_create_user(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    'id'    => 'user-1',
                    'name'  => 'John Doe',
                    'email' => 'john@example.com',
                ],
            ]),
        ]);

        $resource = new UsersResource($this->samsara);
        $user = $resource->create([
            'name'  => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame('John Doe', $user->name);
    }

    #[Test]
    public function it_can_delete_user(): void
    {
        $this->http->fake([
            '*' => $this->http->response([], 204),
        ]);

        $resource = new UsersResource($this->samsara);
        $result = $resource->delete('user-1');

        $this->assertTrue($result);
    }

    #[Test]
    public function it_can_find_user_by_id(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    'id'    => 'user-1',
                    'name'  => 'Jane Doe',
                    'email' => 'jane@example.com',
                ],
            ]),
        ]);

        $resource = new UsersResource($this->samsara);
        $user = $resource->find('user-1');

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame('Jane Doe', $user->name);
    }

    #[Test]
    public function it_can_get_all_users(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    [
                        'id'   => 'user-1',
                        'name' => 'John Doe',
                    ],
                    [
                        'id'   => 'user-2',
                        'name' => 'Jane Doe',
                    ],
                ],
            ]),
        ]);

        $resource = new UsersResource($this->samsara);
        $users = $resource->all();

        $this->assertInstanceOf(EntityCollection::class, $users);
        $this->assertCount(2, $users);
        $this->assertInstanceOf(User::class, $users->first());
    }

    #[Test]
    public function it_can_return_query_builder(): void
    {
        $resource = new UsersResource($this->samsara);
        $query = $resource->query();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_update_user(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    'id'   => 'user-1',
                    'name' => 'Updated User',
                ],
            ]),
        ]);

        $resource = new UsersResource($this->samsara);
        $user = $resource->update('user-1', ['name' => 'Updated User']);

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame('Updated User', $user->name);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new UsersResource($this->samsara);

        $this->assertSame('/users', $resource->getEndpoint());
    }

    #[Test]
    public function it_has_correct_entity_class(): void
    {
        $resource = new UsersResource($this->samsara);

        $this->assertSame(User::class, $resource->getEntityClass());
    }
}
