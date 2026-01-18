<?php

namespace ErikGall\Samsara\Tests\Unit\Resources\Dispatch;

use ErikGall\Samsara\Samsara;
use ErikGall\Samsara\Query\Builder;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Data\Address\Address;
use ErikGall\Samsara\Data\EntityCollection;
use Illuminate\Http\Client\Factory as HttpFactory;
use ErikGall\Samsara\Resources\Dispatch\AddressesResource;

/**
 * Unit tests for the AddressesResource.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class AddressesResourceTest extends TestCase
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
        $resource = new AddressesResource($this->samsara);

        $this->assertInstanceOf(AddressesResource::class, $resource);
    }

    #[Test]
    public function it_can_create_address(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    'id'               => 'addr-1',
                    'name'             => 'Warehouse',
                    'formattedAddress' => '123 Main St',
                ],
            ]),
        ]);

        $resource = new AddressesResource($this->samsara);
        $address = $resource->create([
            'name'             => 'Warehouse',
            'formattedAddress' => '123 Main St',
        ]);

        $this->assertInstanceOf(Address::class, $address);
        $this->assertSame('Warehouse', $address->name);
    }

    #[Test]
    public function it_can_delete_address(): void
    {
        $this->http->fake([
            '*' => $this->http->response([], 204),
        ]);

        $resource = new AddressesResource($this->samsara);
        $result = $resource->delete('addr-1');

        $this->assertTrue($result);
    }

    #[Test]
    public function it_can_find_address_by_id(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    'id'               => 'addr-1',
                    'name'             => 'Headquarters',
                    'formattedAddress' => '456 Corporate Blvd',
                ],
            ]),
        ]);

        $resource = new AddressesResource($this->samsara);
        $address = $resource->find('addr-1');

        $this->assertInstanceOf(Address::class, $address);
        $this->assertSame('Headquarters', $address->name);
    }

    #[Test]
    public function it_can_get_all_addresses(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    [
                        'id'   => 'addr-1',
                        'name' => 'Warehouse A',
                    ],
                    [
                        'id'   => 'addr-2',
                        'name' => 'Warehouse B',
                    ],
                ],
            ]),
        ]);

        $resource = new AddressesResource($this->samsara);
        $addresses = $resource->all();

        $this->assertInstanceOf(EntityCollection::class, $addresses);
        $this->assertCount(2, $addresses);
        $this->assertInstanceOf(Address::class, $addresses->first());
    }

    #[Test]
    public function it_can_return_query_builder(): void
    {
        $resource = new AddressesResource($this->samsara);
        $query = $resource->query();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_update_address(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    'id'   => 'addr-1',
                    'name' => 'Updated Warehouse',
                ],
            ]),
        ]);

        $resource = new AddressesResource($this->samsara);
        $address = $resource->update('addr-1', ['name' => 'Updated Warehouse']);

        $this->assertInstanceOf(Address::class, $address);
        $this->assertSame('Updated Warehouse', $address->name);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new AddressesResource($this->samsara);

        $this->assertSame('/addresses', $resource->getEndpoint());
    }

    #[Test]
    public function it_has_correct_entity_class(): void
    {
        $resource = new AddressesResource($this->samsara);

        $this->assertSame(Address::class, $resource->getEntityClass());
    }
}
