<?php

namespace Samsara\Tests\Unit\Resources\Additional;

use Samsara\Samsara;
use Samsara\Data\Entity;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use Illuminate\Http\Client\Request;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Exceptions\UnsupportedOperationException;
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
    public function it_can_get_query_builder(): void
    {
        $resource = new DriverVehicleAssignmentsResource($this->samsara);

        $builder = $resource->query();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_deletes_assignments_with_the_contract_request_body(): void
    {
        $this->http->fake(['*' => $this->http->response(status: 204)]);
        $payload = ['vehicleId' => 'vehicle-1'];

        $deleted = (new DriverVehicleAssignmentsResource($this->samsara))->deleteAssignments($payload);

        $this->assertTrue($deleted);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'DELETE'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/driver-vehicle-assignments'
            && $request->data() === $payload);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new DriverVehicleAssignmentsResource($this->samsara);

        $this->assertSame('/fleet/driver-vehicle-assignments', $resource->getEndpoint());
    }

    #[Test]
    public function it_rejects_the_deferred_generic_delete_without_sending_a_request(): void
    {
        $this->http->fake();

        $resource = new DriverVehicleAssignmentsResource($this->samsara);

        try {
            $resource->delete('assignment-123');
            $this->fail('Expected the generic delete operation to be rejected.');
        } catch (UnsupportedOperationException $exception) {
            $this->assertSame('delete', $exception->getContext()['operation']);
        }

        $this->http->assertNothingSent();
    }

    #[Test]
    public function it_rejects_the_deferred_generic_update_without_sending_a_request(): void
    {
        $this->http->fake();

        $resource = new DriverVehicleAssignmentsResource($this->samsara);

        try {
            $resource->update('assignment-123', ['vehicleId' => 'vehicle-2']);
            $this->fail('Expected the generic update operation to be rejected.');
        } catch (UnsupportedOperationException $exception) {
            $this->assertSame('update', $exception->getContext()['operation']);
        }

        $this->http->assertNothingSent();
    }

    #[Test]
    public function it_rejects_unfiltered_generic_all_without_sending_a_request(): void
    {
        $this->http->fake();

        $resource = new DriverVehicleAssignmentsResource($this->samsara);

        try {
            $resource->all();
            $this->fail('Expected the generic all operation to be rejected.');
        } catch (UnsupportedOperationException $exception) {
            $this->assertSame('all', $exception->getContext()['operation']);
        }

        $this->http->assertNothingSent();
    }

    #[Test]
    public function it_updates_an_assignment_at_the_collection_endpoint(): void
    {
        $this->http->fake([
            '*' => $this->http->response(['data' => ['message' => 'updated']], 202),
        ]);
        $payload = [
            'driverId'  => 'driver-1',
            'startTime' => '2026-08-14T12:00:00Z',
            'vehicleId' => 'vehicle-1',
        ];

        $assignment = (new DriverVehicleAssignmentsResource($this->samsara))->updateAssignment($payload);

        $this->assertInstanceOf(Entity::class, $assignment);
        $this->assertSame('updated', $assignment->message);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/driver-vehicle-assignments'
            && $request->data() === $payload);
    }
}
