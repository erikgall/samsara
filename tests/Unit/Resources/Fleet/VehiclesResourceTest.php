<?php

namespace Samsara\Tests\Unit\Resources\Fleet;

use Samsara\Samsara;
use Samsara\Tests\TestCase;
use Samsara\Data\Vehicle\Vehicle;
use Samsara\Data\EntityCollection;
use Illuminate\Http\Client\Request;
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
        $this->http->preventStrayRequests();
        $this->samsara = new Samsara('test-token');
        $this->samsara->setHttpFactory($this->http);
    }

    #[Test]
    public function it_can_find_vehicle_by_external_id(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    'id'          => '123',
                    'name'        => 'Truck 001',
                    'externalIds' => ['fleet_code' => 'FL-123'],
                ],
            ]),
        ]);

        $resource = new VehiclesResource($this->samsara);
        $vehicle = $resource->findByExternalId('fleet_code', 'FL-123');

        $this->assertInstanceOf(Vehicle::class, $vehicle);
        $this->assertSame('123', $vehicle->id);
        $this->http->assertSent(fn (Request $request) => $request->method() === 'GET'
            && $request->url() === 'https://api.samsara.com/fleet/vehicles/fleet_code:FL-123'
            && $request->data() === []);
    }

    #[Test]
    public function it_can_find_vehicle_by_id(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    'id'                 => '123',
                    'name'               => 'Truck 001',
                    'vin'                => 'VIN123',
                    'grossVehicleWeight' => ['unit' => 'lb', 'weight' => 26000],
                ],
            ]),
        ]);

        $resource = new VehiclesResource($this->samsara);
        $vehicle = $resource->find('123');

        $this->assertInstanceOf(Vehicle::class, $vehicle);
        $this->assertSame('123', $vehicle->id);
        $this->assertSame('Truck 001', $vehicle->name);
        $this->assertSame(26000, $vehicle->grossVehicleWeight['weight']);
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
    public function it_encodes_external_id_parts_as_one_path_segment(): void
    {
        $this->http->fake([
            '*' => $this->http->response(['data' => ['id' => '123']]),
        ]);

        $resource = new VehiclesResource($this->samsara);
        $resource->findByExternalId('fleet code', 'north/east');

        $this->http->assertSent(fn (Request $request): bool => parse_url($request->url(), PHP_URL_PATH)
            === '/fleet/vehicles/fleet%20code:north%2Feast');
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
    public function it_queries_vehicles(): void
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
        $vehicles = $resource->query()->get();

        $this->assertInstanceOf(EntityCollection::class, $vehicles);
        $this->assertCount(2, $vehicles);
        $this->assertInstanceOf(Vehicle::class, $vehicles->first());
        $this->assertSame('Truck 001', $vehicles->first()->name);
    }

    #[Test]
    public function it_returns_null_when_external_id_not_found(): void
    {
        $this->http->fake([
            '*' => $this->http->response([], 404),
        ]);

        $resource = new VehiclesResource($this->samsara);
        $vehicle = $resource->findByExternalId('fleet_code', 'NONEXISTENT');

        $this->assertNull($vehicle);
        $this->http->assertSent(fn (Request $request) => $request->url()
            === 'https://api.samsara.com/fleet/vehicles/fleet_code:NONEXISTENT');
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

    #[Test]
    public function it_terminal_executes_the_current_vehicle_list_query(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data'       => [['id' => 'vehicle-1', 'name' => 'Truck A']],
                'pagination' => ['endCursor' => '', 'hasNextPage' => false],
            ]),
        ]);

        $page = (new VehiclesResource($this->samsara))
            ->query()
            ->whereParentTag(['parent-1', 'parent-2'])
            ->whereTag(['tag-1', 'tag-2'])
            ->whereAttribute(['attribute-1', 'attribute-2'])
            ->where('attributes', ['make', 'model'])
            ->updatedAfter('2026-08-10T14:00:00Z')
            ->createdAfter('2026-08-09T14:00:00Z')
            ->after('vehicle-cursor')
            ->paginate(50);

        $this->assertInstanceOf(Vehicle::class, $page->items()->first());
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && $request->url()
            === 'https://api.samsara.com/fleet/vehicles?attributes=make&attributes=model&parentTagIds=parent-1%2Cparent-2&tagIds=tag-1%2Ctag-2&attributeValueIds=attribute-1%2Cattribute-2&updatedAfterTime=2026-08-10T14%3A00%3A00Z&createdAfterTime=2026-08-09T14%3A00%3A00Z&limit=50&after=vehicle-cursor'
        );
    }
}
