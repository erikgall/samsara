<?php

namespace Samsara\Tests\Unit\Resources\Telematics;

use Samsara\Samsara;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use Samsara\Data\EntityCollection;
use Illuminate\Http\Client\Request;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\Vehicle\VehicleLocationResult;
use Samsara\Data\Vehicle\VehicleLocationSeries;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Resources\Telematics\VehicleLocationsResource;

/**
 * Unit tests for the VehicleLocationsResource.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class VehicleLocationsResourceTest extends TestCase
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
    public function it_can_return_current_query_builder(): void
    {
        $resource = new VehicleLocationsResource($this->samsara);
        $query = $resource->current();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_return_feed_query_builder(): void
    {
        $resource = new VehicleLocationsResource($this->samsara);
        $query = $resource->feed();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_return_history_query_builder(): void
    {
        $resource = new VehicleLocationsResource($this->samsara);
        $query = $resource->history();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_return_query_builder(): void
    {
        $resource = new VehicleLocationsResource($this->samsara);
        $query = $resource->query();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_feed_hits_correct_endpoint(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [[
                    'id'        => 'vehicle-2',
                    'name'      => 'Truck 2',
                    'locations' => [[
                        'latitude'  => 40.7128,
                        'longitude' => -74.0060,
                        'time'      => '2026-08-10T15:05:00Z',
                    ]],
                ]],
                'pagination' => ['endCursor' => 'next-feed', 'hasNextPage' => false],
            ]),
        ]);

        $resource = new VehicleLocationsResource($this->samsara);
        $locations = $resource->feed()
            ->whereVehicle(['vehicle-1', 'vehicle-2'])
            ->after('feed-cursor')
            ->get();

        $this->assertInstanceOf(EntityCollection::class, $locations);
        $this->assertCount(1, $locations);
        $this->assertInstanceOf(VehicleLocationSeries::class, $locations->first());
        $this->assertSame('vehicle-2', $locations->first()->getId());
        $this->assertSame(40.7128, $locations->first()->getLocations()->first()->latitude);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/vehicles/locations/feed'
            && $request->data() === [
                'vehicleIds' => 'vehicle-1,vehicle-2',
                'after'      => 'feed-cursor',
            ]
        );
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new VehicleLocationsResource($this->samsara);

        $this->assertSame('/fleet/vehicles/locations', $resource->getEndpoint());
    }

    #[Test]
    public function it_history_hits_correct_endpoint(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [[
                    'id'        => 'vehicle-1',
                    'name'      => 'Truck 1',
                    'locations' => [[
                        'latitude'  => 37.7749,
                        'longitude' => -122.4194,
                        'time'      => '2026-08-10T14:05:00Z',
                    ]],
                ]],
                'pagination' => ['endCursor' => 'next-history', 'hasNextPage' => false],
            ]),
        ]);

        $resource = new VehicleLocationsResource($this->samsara);
        $locations = $resource->history()
            ->between('2026-08-10T14:00:00Z', '2026-08-10T15:00:00Z')
            ->whereVehicle('vehicle-1')
            ->after('history-cursor')
            ->get();

        $this->assertInstanceOf(EntityCollection::class, $locations);
        $this->assertCount(1, $locations);
        $this->assertInstanceOf(VehicleLocationSeries::class, $locations->first());
        $this->assertSame('vehicle-1', $locations->first()->getId());
        $this->assertSame(-122.4194, $locations->first()->getLocations()->first()->longitude);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/vehicles/locations/history'
            && $request->data() === [
                'startTime'  => '2026-08-10T14:00:00Z',
                'endTime'    => '2026-08-10T15:00:00Z',
                'vehicleIds' => 'vehicle-1',
                'after'      => 'history-cursor',
            ]
        );
    }

    #[Test]
    public function it_queries_the_snapshot_contract(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [[
                    'id'       => 'vehicle-1',
                    'name'     => 'Truck 1',
                    'location' => [
                        'latitude'  => 35.8456,
                        'longitude' => -86.3903,
                        'time'      => '2026-08-10T15:00:00Z',
                    ],
                ]],
                'pagination' => ['endCursor' => '', 'hasNextPage' => false],
            ]),
        ]);

        $locations = (new VehicleLocationsResource($this->samsara))->query()->get();

        $this->assertInstanceOf(VehicleLocationResult::class, $locations->first());
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/vehicles/locations'
            && $request->data() === []
        );
    }

    #[Test]
    public function it_sends_the_exact_current_location_request_and_maps_vehicle_wrappers(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [[
                    'id'       => 'vehicle-1',
                    'name'     => 'Truck 1',
                    'location' => [
                        'latitude'  => 35.8456,
                        'longitude' => -86.3903,
                        'time'      => '2026-08-10T15:00:00Z',
                    ],
                ]],
                'pagination' => ['endCursor' => 'next-1', 'hasNextPage' => false],
            ]),
        ]);

        $locations = (new VehicleLocationsResource($this->samsara))
            ->current()
            ->whereVehicle(['vehicle-1', 'vehicle-2'])
            ->whereTag(['tag-1', 'tag-2'])
            ->whereParentTag('parent-1')
            ->where('time', '2026-08-10T15:00:00Z')
            ->after('cursor-1')
            ->get();

        $this->assertInstanceOf(VehicleLocationResult::class, $locations->first());
        $this->assertSame('Truck 1', $locations->first()->name);
        $this->assertSame(35.8456, $locations->first()->getLocation()->latitude);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/vehicles/locations'
            && $request->data() == [
                'time'         => '2026-08-10T15:00:00Z',
                'parentTagIds' => 'parent-1',
                'tagIds'       => 'tag-1,tag-2',
                'vehicleIds'   => 'vehicle-1,vehicle-2',
                'after'        => 'cursor-1',
            ]
        );
    }

    #[Test]
    public function it_terminal_executes_the_base_query_with_the_snapshot_contract(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [[
                    'id'       => 'vehicle-1',
                    'name'     => 'Truck 1',
                    'location' => [
                        'latitude'  => 35.8456,
                        'longitude' => -86.3903,
                        'time'      => '2026-08-10T15:00:00Z',
                    ],
                ]],
                'pagination' => ['endCursor' => '', 'hasNextPage' => false],
            ]),
        ]);

        $locations = (new VehicleLocationsResource($this->samsara))->query()->get();

        $this->assertInstanceOf(VehicleLocationResult::class, $locations->first());
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/vehicles/locations'
            && $request->data() === []
        );
    }
}
