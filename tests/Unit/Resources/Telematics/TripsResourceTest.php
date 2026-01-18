<?php

namespace ErikGall\Samsara\Tests\Unit\Resources\Telematics;

use ErikGall\Samsara\Samsara;
use ErikGall\Samsara\Query\Builder;
use ErikGall\Samsara\Data\Trip\Trip;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Data\EntityCollection;
use Illuminate\Http\Client\Factory as HttpFactory;
use ErikGall\Samsara\Resources\Telematics\TripsResource;

/**
 * Unit tests for the TripsResource.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class TripsResourceTest extends TestCase
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
        $resource = new TripsResource($this->samsara);

        $this->assertInstanceOf(TripsResource::class, $resource);
    }

    #[Test]
    public function it_can_filter_by_completion_status(): void
    {
        $resource = new TripsResource($this->samsara);
        $query = $resource->completed();

        $this->assertInstanceOf(Builder::class, $query);
        $queryParams = $query->buildQuery();
        $this->assertSame('completed', $queryParams['completionStatus'] ?? null);
    }

    #[Test]
    public function it_can_filter_in_progress_trips(): void
    {
        $resource = new TripsResource($this->samsara);
        $query = $resource->inProgress();

        $this->assertInstanceOf(Builder::class, $query);
        $queryParams = $query->buildQuery();
        $this->assertSame('inProgress', $queryParams['completionStatus'] ?? null);
    }

    #[Test]
    public function it_can_get_all_trips(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    [
                        'startTime'      => '2024-01-01T08:00:00Z',
                        'endTime'        => '2024-01-01T10:00:00Z',
                        'distanceMeters' => 50000,
                        'drivingTimeMs'  => 7200000,
                    ],
                    [
                        'startTime'      => '2024-01-01T12:00:00Z',
                        'endTime'        => '2024-01-01T14:00:00Z',
                        'distanceMeters' => 75000,
                        'drivingTimeMs'  => 5400000,
                    ],
                ],
            ]),
        ]);

        $resource = new TripsResource($this->samsara);
        $trips = $resource->all();

        $this->assertInstanceOf(EntityCollection::class, $trips);
        $this->assertCount(2, $trips);
        $this->assertInstanceOf(Trip::class, $trips->first());
    }

    #[Test]
    public function it_can_return_query_builder(): void
    {
        $resource = new TripsResource($this->samsara);
        $query = $resource->query();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new TripsResource($this->samsara);

        $this->assertSame('/trips/stream', $resource->getEndpoint());
    }

    #[Test]
    public function it_has_correct_entity_class(): void
    {
        $resource = new TripsResource($this->samsara);

        $this->assertSame(Trip::class, $resource->getEntityClass());
    }
}
