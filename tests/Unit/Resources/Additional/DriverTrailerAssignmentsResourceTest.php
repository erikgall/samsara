<?php

namespace ErikGall\Samsara\Tests\Unit\Resources\Additional;

use ErikGall\Samsara\Samsara;
use ErikGall\Samsara\Data\Entity;
use ErikGall\Samsara\Query\Builder;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Data\EntityCollection;
use Illuminate\Http\Client\Factory as HttpFactory;
use ErikGall\Samsara\Resources\Additional\DriverTrailerAssignmentsResource;

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
    public function it_can_get_all_assignments(): void
    {
        $this->http->fake([
            '*/driver-trailer-assignments' => $this->http->response([
                'data' => [
                    ['id' => 'assignment-1'],
                    ['id' => 'assignment-2'],
                ],
            ], 200),
        ]);

        $resource = new DriverTrailerAssignmentsResource($this->samsara);

        $result = $resource->all();

        $this->assertInstanceOf(EntityCollection::class, $result);
        $this->assertCount(2, $result);
    }

    #[Test]
    public function it_can_get_query_builder(): void
    {
        $resource = new DriverTrailerAssignmentsResource($this->samsara);

        $builder = $resource->query();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_update_assignment(): void
    {
        $this->http->fake([
            '*/driver-trailer-assignments/assignment-123' => $this->http->response([
                'data' => [
                    'id' => 'assignment-123',
                ],
            ], 200),
        ]);

        $resource = new DriverTrailerAssignmentsResource($this->samsara);

        $result = $resource->update('assignment-123', ['trailerId' => 'trailer-2']);

        $this->assertInstanceOf(Entity::class, $result);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new DriverTrailerAssignmentsResource($this->samsara);

        $this->assertSame('/driver-trailer-assignments', $resource->getEndpoint());
    }
}
