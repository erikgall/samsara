<?php

namespace Samsara\Tests\Unit\Resources\Additional;

use Samsara\Samsara;
use Samsara\Data\Entity;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Exceptions\UnsupportedOperationException;
use Samsara\Resources\Additional\DriverTrailerAssignmentsResource;

/**
 * Unit tests for the DriverTrailerAssignmentsResource class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class DriverTrailerAssignmentsResourceTest extends TestCase
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
            '*/driver-trailer-assignments' => $this->http->response([
                'data' => [
                    'id' => 'assignment-123',
                ],
            ], 201),
        ]);

        $resource = new DriverTrailerAssignmentsResource($this->samsara);

        $result = $resource->create(['driverId' => 'driver-1', 'trailerId' => 'trailer-1']);

        $this->assertInstanceOf(Entity::class, $result);
    }

    #[Test]
    public function it_can_get_query_builder(): void
    {
        $resource = new DriverTrailerAssignmentsResource($this->samsara);

        $builder = $resource->query();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new DriverTrailerAssignmentsResource($this->samsara);

        $this->assertSame('/driver-trailer-assignments', $resource->getEndpoint());
    }

    #[Test]
    public function it_rejects_the_deferred_generic_update_without_sending_a_request(): void
    {
        $this->http->fake();

        $resource = new DriverTrailerAssignmentsResource($this->samsara);

        try {
            $resource->update('assignment-123', ['trailerId' => 'trailer-2']);
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

        $resource = new DriverTrailerAssignmentsResource($this->samsara);

        try {
            $resource->all();
            $this->fail('Expected the generic all operation to be rejected.');
        } catch (UnsupportedOperationException $exception) {
            $this->assertSame('all', $exception->getContext()['operation']);
        }

        $this->http->assertNothingSent();
    }
}
