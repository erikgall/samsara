<?php

namespace Samsara\Tests\Unit\Resources\Telematics;

use Samsara\Samsara;
use Samsara\Query\Builder;
use Samsara\Data\Trip\Trip;
use Samsara\Tests\TestCase;
use Illuminate\Http\Client\Request;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Resources\Telematics\TripsResource;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Exceptions\UnsupportedOperationException;

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
        $resource = new TripsResource($this->samsara);

        try {
            $resource->all();
            $this->fail('Trips all() cannot supply the required startTime and ids query.');
        } catch (UnsupportedOperationException $exception) {
            $this->assertSame('all', $exception->getContext()['operation'] ?? null);
        }

        $this->http->assertNothingSent();
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

    #[Test]
    public function it_paginates_the_base_trip_query_without_an_unsupported_limit(): void
    {
        $this->http->fakeSequence()
            ->push([
                'data' => [[
                    'tripStartTime'    => '2026-08-10T14:00:00Z',
                    'createdAtTime'    => '2026-08-10T14:00:05Z',
                    'updatedAtTime'    => '2026-08-10T14:15:00Z',
                    'completionStatus' => 'inProgress',
                    'asset'            => ['id' => 'vehicle-1', 'name' => 'Truck 1'],
                    'startLocation'    => ['latitude' => 35.8, 'longitude' => -86.3],
                ]],
                'pagination' => ['endCursor' => 'trip-next', 'hasNextPage' => true],
            ])
            ->push([
                'data'       => [],
                'pagination' => ['endCursor' => '', 'hasNextPage' => false],
            ]);

        $page = (new TripsResource($this->samsara))
            ->query()
            ->where('includeAsset', true)
            ->where('completionStatus', 'all')
            ->between('2026-08-10T14:00:00Z', '2026-08-10T15:00:00Z')
            ->where('queryBy', 'tripStartTime')
            ->where('ids', 'vehicle-1')
            ->paginate();
        $page->nextPage();

        $this->assertInstanceOf(Trip::class, $page->items()->first());
        $this->http->assertSentCount(2);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/trips/stream'
            && $request->data() === [
                'includeAsset'     => 'true',
                'completionStatus' => 'all',
                'startTime'        => '2026-08-10T14:00:00Z',
                'endTime'          => '2026-08-10T15:00:00Z',
                'queryBy'          => 'tripStartTime',
                'ids'              => 'vehicle-1',
            ]
        );
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/trips/stream'
            && $request->data() === [
                'includeAsset'     => 'true',
                'completionStatus' => 'all',
                'startTime'        => '2026-08-10T14:00:00Z',
                'endTime'          => '2026-08-10T15:00:00Z',
                'queryBy'          => 'tripStartTime',
                'ids'              => 'vehicle-1',
                'after'            => 'trip-next',
            ]
        );
    }

    #[Test]
    public function it_terminal_executes_completed_trips_with_required_query(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [[
                    'tripStartTime'    => '2026-08-10T14:00:00Z',
                    'tripEndTime'      => '2026-08-10T14:30:00Z',
                    'createdAtTime'    => '2026-08-10T14:00:05Z',
                    'updatedAtTime'    => '2026-08-10T14:30:05Z',
                    'completionStatus' => 'completed',
                    'asset'            => ['id' => 'vehicle-1', 'name' => 'Truck 1'],
                    'startLocation'    => ['latitude' => 35.8, 'longitude' => -86.3],
                    'endLocation'      => ['latitude' => 35.9, 'longitude' => -86.4],
                ]],
                'pagination' => ['endCursor' => '', 'hasNextPage' => false],
            ]),
        ]);

        $page = (new TripsResource($this->samsara))
            ->completed()
            ->startTime('2026-08-10T14:00:00Z')
            ->where('ids', 'vehicle-1,vehicle-2')
            ->paginate();

        $trip = $page->items()->first();
        $this->assertInstanceOf(Trip::class, $trip);
        $this->assertSame('2026-08-10T14:00:00Z', $trip->tripStartTime);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/trips/stream'
            && $request->data() === [
                'completionStatus' => 'completed',
                'startTime'        => '2026-08-10T14:00:00Z',
                'ids'              => 'vehicle-1,vehicle-2',
            ]
        );
    }

    #[Test]
    public function it_terminal_executes_in_progress_trips_with_required_query(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [[
                    'tripStartTime'    => '2026-08-10T14:00:00Z',
                    'createdAtTime'    => '2026-08-10T14:00:05Z',
                    'updatedAtTime'    => '2026-08-10T14:15:00Z',
                    'completionStatus' => 'inProgress',
                    'asset'            => ['id' => 'vehicle-1', 'name' => 'Truck 1'],
                    'startLocation'    => ['latitude' => 35.8, 'longitude' => -86.3],
                ]],
                'pagination' => ['endCursor' => '', 'hasNextPage' => false],
            ]),
        ]);

        $trips = (new TripsResource($this->samsara))
            ->inProgress()
            ->startTime('2026-08-10T14:00:00Z')
            ->where('ids', 'vehicle-1')
            ->get();

        $this->assertInstanceOf(Trip::class, $trips->first());
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/trips/stream'
            && $request->data() === [
                'completionStatus' => 'inProgress',
                'startTime'        => '2026-08-10T14:00:00Z',
                'ids'              => 'vehicle-1',
            ]
        );
    }
}
