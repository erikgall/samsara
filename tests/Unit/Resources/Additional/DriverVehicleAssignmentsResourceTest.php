<?php

namespace Samsara\Tests\Unit\Resources\Additional;

use Samsara\Samsara;
use Samsara\Data\Entity;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use Samsara\Data\EntityCollection;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Resources\Additional\DriverVehicleAssignmentsResource;

/**
 * Unit tests for the DriverVehicleAssignmentsResource class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class DriverVehicleAssignmentsResourceTest extends TestCase
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
    public function it_can_create_assignment(): void
    {
        $this->http->fake([
            '*/fleet/driver-vehicle-assignments' => $this->http->response([
                'data' => [
                    'id' => 'assignment-123',
                ],
            ], 201),
        ]);

        $resource = new DriverVehicleAssignmentsResource($this->samsara);

        $result = $resource->create(['driverId' => 'driver-1', 'vehicleId' => 'vehicle-1']);

        $this->assertInstanceOf(Entity::class, $result);
    }

    #[Test]
    public function it_can_delete_assignment(): void
    {
        $this->http->fake([
            '*/fleet/driver-vehicle-assignments/assignment-123' => $this->http->response([], 204),
        ]);

        $resource = new DriverVehicleAssignmentsResource($this->samsara);

        $result = $resource->delete('assignment-123');

        $this->assertTrue($result);
    }

    #[Test]
    public function it_can_get_all_assignments(): void
    {
        $this->http->fake([
            '*/fleet/driver-vehicle-assignments' => $this->http->response([
                'data' => [
                    ['id' => 'assignment-1'],
                    ['id' => 'assignment-2'],
                ],
            ], 200),
        ]);

        $resource = new DriverVehicleAssignmentsResource($this->samsara);

        $result = $resource->all();

        $this->assertInstanceOf(EntityCollection::class, $result);
        $this->assertCount(2, $result);
    }

    #[Test]
    public function it_can_get_query_builder(): void
    {
        $resource = new DriverVehicleAssignmentsResource($this->samsara);

        $builder = $resource->query();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_update_assignment(): void
    {
        $this->http->fake([
            '*/fleet/driver-vehicle-assignments/assignment-123' => $this->http->response([
                'data' => [
                    'id' => 'assignment-123',
                ],
            ], 200),
        ]);

        $resource = new DriverVehicleAssignmentsResource($this->samsara);

        $result = $resource->update('assignment-123', ['vehicleId' => 'vehicle-2']);

        $this->assertInstanceOf(Entity::class, $result);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new DriverVehicleAssignmentsResource($this->samsara);

        $this->assertSame('/fleet/driver-vehicle-assignments', $resource->getEndpoint());
    }
}
