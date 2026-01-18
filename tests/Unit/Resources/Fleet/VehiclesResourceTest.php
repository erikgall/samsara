<?php

namespace Samsara\Tests\Unit\Resources\Fleet;

use Samsara\Samsara;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use Samsara\Data\Vehicle\Vehicle;
use Samsara\Data\EntityCollection;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Resources\Fleet\VehiclesResource;
use Illuminate\Http\Client\Factory as HttpFactory;

/**
 * Unit tests for the VehiclesResource.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class VehiclesResourceTest extends TestCase
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
        $resource = new VehiclesResource($this->samsara);

        $this->assertInstanceOf(VehiclesResource::class, $resource);
    }

    #[Test]
    public function it_can_create_vehicle(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    'id'   => '123',
                    'name' => 'New Vehicle',
                    'vin'  => 'VIN789',
                ],
            ]),
        ]);

        $resource = new VehiclesResource($this->samsara);
        $vehicle = $resource->create([
            'name' => 'New Vehicle',
            'vin'  => 'VIN789',
        ]);

        $this->assertInstanceOf(Vehicle::class, $vehicle);
        $this->assertSame('123', $vehicle->id);
        $this->assertSame('New Vehicle', $vehicle->name);
    }

    #[Test]
    public function it_can_delete_vehicle(): void
    {
        $this->http->fake([
            '*' => $this->http->response([]),
        ]);

        $resource = new VehiclesResource($this->samsara);
        $result = $resource->delete('123');

        $this->assertTrue($result);
    }

    #[Test]
    public function it_can_find_vehicle_by_external_id(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    [
                        'id'          => '123',
                        'name'        => 'Truck 001',
                        'externalIds' => ['fleet_code' => 'FL-123'],
                    ],
                ],
            ]),
        ]);

        $resource = new VehiclesResource($this->samsara);
        $vehicle = $resource->findByExternalId('fleet_code', 'FL-123');

        $this->assertInstanceOf(Vehicle::class, $vehicle);
        $this->assertSame('123', $vehicle->id);
    }

    #[Test]
    public function it_can_find_vehicle_by_id(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => ['id' => '123', 'name' => 'Truck 001', 'vin' => 'VIN123'],
            ]),
        ]);

        $resource = new VehiclesResource($this->samsara);
        $vehicle = $resource->find('123');

        $this->assertInstanceOf(Vehicle::class, $vehicle);
        $this->assertSame('123', $vehicle->id);
        $this->assertSame('Truck 001', $vehicle->name);
    }

    #[Test]
    public function it_can_get_all_vehicles(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    ['id' => '1', 'name' => 'Truck 001', 'vin' => 'VIN123'],
                    ['id' => '2', 'name' => 'Truck 002', 'vin' => 'VIN456'],
                ],
            ]),
        ]);

        $resource = new VehiclesResource($this->samsara);
        $vehicles = $resource->all();

        $this->assertInstanceOf(EntityCollection::class, $vehicles);
        $this->assertCount(2, $vehicles);
        $this->assertInstanceOf(Vehicle::class, $vehicles->first());
        $this->assertSame('Truck 001', $vehicles->first()->name);
    }

    #[Test]
    public function it_can_return_query_builder(): void
    {
        $resource = new VehiclesResource($this->samsara);
        $query = $resource->query();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_update_vehicle(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    'id'   => '123',
                    'name' => 'Updated Vehicle',
                ],
            ]),
        ]);

        $resource = new VehiclesResource($this->samsara);
        $vehicle = $resource->update('123', ['name' => 'Updated Vehicle']);

        $this->assertInstanceOf(Vehicle::class, $vehicle);
        $this->assertSame('Updated Vehicle', $vehicle->name);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new VehiclesResource($this->samsara);

        $this->assertSame('/fleet/vehicles', $resource->getEndpoint());
    }

    #[Test]
    public function it_has_correct_entity_class(): void
    {
        $resource = new VehiclesResource($this->samsara);

        $this->assertSame(Vehicle::class, $resource->getEntityClass());
    }

    #[Test]
    public function it_returns_null_when_external_id_not_found(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [],
            ]),
        ]);

        $resource = new VehiclesResource($this->samsara);
        $vehicle = $resource->findByExternalId('fleet_code', 'NONEXISTENT');

        $this->assertNull($vehicle);
    }

    #[Test]
    public function it_returns_null_when_vehicle_not_found(): void
    {
        $this->http->fake([
            '*' => $this->http->response([], 404),
        ]);

        $resource = new VehiclesResource($this->samsara);
        $vehicle = $resource->find('999');

        $this->assertNull($vehicle);
    }
}
