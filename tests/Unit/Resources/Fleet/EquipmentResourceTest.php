<?php

namespace Samsara\Tests\Unit\Resources\Fleet;

use Samsara\Samsara;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use Samsara\Data\EntityCollection;
use Samsara\Data\Equipment\Equipment;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Resources\Fleet\EquipmentResource;
use Illuminate\Http\Client\Factory as HttpFactory;

/**
 * Unit tests for the EquipmentResource.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class EquipmentResourceTest extends TestCase
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
        $resource = new EquipmentResource($this->samsara);

        $this->assertInstanceOf(EquipmentResource::class, $resource);
    }

    #[Test]
    public function it_can_create_equipment(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    'id'   => '123',
                    'name' => 'New Equipment',
                ],
            ]),
        ]);

        $resource = new EquipmentResource($this->samsara);
        $equipment = $resource->create([
            'name' => 'New Equipment',
        ]);

        $this->assertInstanceOf(Equipment::class, $equipment);
        $this->assertSame('123', $equipment->id);
        $this->assertSame('New Equipment', $equipment->name);
    }

    #[Test]
    public function it_can_delete_equipment(): void
    {
        $this->http->fake([
            '*' => $this->http->response([]),
        ]);

        $resource = new EquipmentResource($this->samsara);
        $result = $resource->delete('123');

        $this->assertTrue($result);
    }

    #[Test]
    public function it_can_find_equipment_by_external_id(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    [
                        'id'          => '123',
                        'name'        => 'Equipment 001',
                        'externalIds' => ['asset_code' => 'EQ-123'],
                    ],
                ],
            ]),
        ]);

        $resource = new EquipmentResource($this->samsara);
        $equipment = $resource->findByExternalId('asset_code', 'EQ-123');

        $this->assertInstanceOf(Equipment::class, $equipment);
        $this->assertSame('123', $equipment->id);
    }

    #[Test]
    public function it_can_find_equipment_by_id(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => ['id' => '123', 'name' => 'Equipment 001'],
            ]),
        ]);

        $resource = new EquipmentResource($this->samsara);
        $equipment = $resource->find('123');

        $this->assertInstanceOf(Equipment::class, $equipment);
        $this->assertSame('123', $equipment->id);
        $this->assertSame('Equipment 001', $equipment->name);
    }

    #[Test]
    public function it_can_get_all_equipment(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    ['id' => '1', 'name' => 'Equipment 001'],
                    ['id' => '2', 'name' => 'Equipment 002'],
                ],
            ]),
        ]);

        $resource = new EquipmentResource($this->samsara);
        $equipment = $resource->all();

        $this->assertInstanceOf(EntityCollection::class, $equipment);
        $this->assertCount(2, $equipment);
        $this->assertInstanceOf(Equipment::class, $equipment->first());
        $this->assertSame('Equipment 001', $equipment->first()->name);
    }

    #[Test]
    public function it_can_return_locations_feed_query_builder(): void
    {
        $resource = new EquipmentResource($this->samsara);
        $query = $resource->locationsFeed();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_return_locations_history_query_builder(): void
    {
        $resource = new EquipmentResource($this->samsara);
        $query = $resource->locationsHistory();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_return_locations_query_builder(): void
    {
        $resource = new EquipmentResource($this->samsara);
        $query = $resource->locations();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_return_query_builder(): void
    {
        $resource = new EquipmentResource($this->samsara);
        $query = $resource->query();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_return_stats_feed_query_builder(): void
    {
        $resource = new EquipmentResource($this->samsara);
        $query = $resource->statsFeed();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_return_stats_history_query_builder(): void
    {
        $resource = new EquipmentResource($this->samsara);
        $query = $resource->statsHistory();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_return_stats_query_builder(): void
    {
        $resource = new EquipmentResource($this->samsara);
        $query = $resource->stats();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_update_equipment(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    'id'   => '123',
                    'name' => 'Updated Equipment',
                ],
            ]),
        ]);

        $resource = new EquipmentResource($this->samsara);
        $equipment = $resource->update('123', ['name' => 'Updated Equipment']);

        $this->assertInstanceOf(Equipment::class, $equipment);
        $this->assertSame('Updated Equipment', $equipment->name);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new EquipmentResource($this->samsara);

        $this->assertSame('/fleet/equipment', $resource->getEndpoint());
    }

    #[Test]
    public function it_has_correct_entity_class(): void
    {
        $resource = new EquipmentResource($this->samsara);

        $this->assertSame(Equipment::class, $resource->getEntityClass());
    }

    #[Test]
    public function it_returns_null_when_equipment_not_found(): void
    {
        $this->http->fake([
            '*' => $this->http->response([], 404),
        ]);

        $resource = new EquipmentResource($this->samsara);
        $equipment = $resource->find('999');

        $this->assertNull($equipment);
    }

    #[Test]
    public function it_returns_null_when_external_id_not_found(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [],
            ]),
        ]);

        $resource = new EquipmentResource($this->samsara);
        $equipment = $resource->findByExternalId('asset_code', 'NONEXISTENT');

        $this->assertNull($equipment);
    }
}
