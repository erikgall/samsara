<?php

namespace Samsara\Tests\Unit\Resources\Telematics;

use Samsara\Samsara;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use Samsara\Data\EntityCollection;
use Illuminate\Http\Client\Request;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\Vehicle\VehicleStats;
use Samsara\Data\Vehicle\VehicleStatsSeries;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Exceptions\UnsupportedOperationException;
use Samsara\Resources\Telematics\VehicleStatsResource;

/**
 * Unit tests for the VehicleStatsResource.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class VehicleStatsResourceTest extends TestCase
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
        $resource = new VehicleStatsResource($this->samsara);

        $this->assertInstanceOf(VehicleStatsResource::class, $resource);
    }

    #[Test]
    public function it_can_create_engine_states_query_with_types(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [[
                    'id'          => 'vehicle-1',
                    'name'        => 'Truck 1',
                    'engineState' => ['value' => 'On', 'time' => '2026-08-10T15:00:00Z'],
                ]],
                'pagination' => ['endCursor' => '', 'hasNextPage' => false],
            ]),
        ]);

        $resource = new VehicleStatsResource($this->samsara);
        $page = $resource->engineStates()->paginate();

        $this->assertInstanceOf(VehicleStats::class, $page->items()->first());
        $this->assertSame('On', $page->items()->first()->engineState['value']);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/vehicles/stats'
            && $request->data() === ['types' => 'engineStates']
        );
    }

    #[Test]
    public function it_can_create_fuel_percents_query_with_types(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [[
                    'id'          => 'vehicle-1',
                    'name'        => 'Truck 1',
                    'fuelPercent' => ['value' => 75, 'time' => '2026-08-10T15:00:00Z'],
                ]],
                'pagination' => ['endCursor' => '', 'hasNextPage' => false],
            ]),
        ]);

        $resource = new VehicleStatsResource($this->samsara);
        $page = $resource->fuelPercents()->paginate();

        $this->assertSame(75, $page->items()->first()->getFuelPercent());
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/vehicles/stats'
            && $request->data() === ['types' => 'fuelPercents']
        );
    }

    #[Test]
    public function it_can_create_gps_query_with_types(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [[
                    'id'   => 'vehicle-1',
                    'name' => 'Truck 1',
                    'gps'  => [
                        'latitude'          => 35.8,
                        'longitude'         => -86.3,
                        'time'              => '2026-08-10T15:00:00Z',
                        'headingDegrees'    => 180,
                        'speedMilesPerHour' => 40.0,
                    ],
                ]],
                'pagination' => ['endCursor' => '', 'hasNextPage' => false],
            ]),
        ]);

        $resource = new VehicleStatsResource($this->samsara);
        $page = $resource->gps()->paginate();

        $this->assertSame(35.8, $page->items()->first()->getGps()->latitude);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/vehicles/stats'
            && $request->data() === ['types' => 'gps']
        );
    }

    #[Test]
    public function it_can_create_odometer_query_with_types(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [[
                    'id'                => 'vehicle-1',
                    'name'              => 'Truck 1',
                    'obdOdometerMeters' => ['value' => 1000, 'time' => '2026-08-10T15:00:00Z'],
                    'gpsOdometerMeters' => ['value' => 1001, 'time' => '2026-08-10T15:00:00Z'],
                ]],
                'pagination' => ['endCursor' => '', 'hasNextPage' => false],
            ]),
        ]);

        $resource = new VehicleStatsResource($this->samsara);
        $page = $resource->odometer()->paginate();

        $this->assertSame(1000, $page->items()->first()->getObdOdometerMeters());
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/vehicles/stats'
            && $request->data() === ['types' => 'obdOdometerMeters,gpsOdometerMeters']
        );
    }

    #[Test]
    public function it_can_get_all_stats(): void
    {
        $resource = new VehicleStatsResource($this->samsara);

        try {
            $resource->all();
            $this->fail('Vehicle stats all() cannot supply required types.');
        } catch (UnsupportedOperationException) {
            $this->assertTrue(true);
        }

        $this->http->assertNothingSent();
    }

    #[Test]
    public function it_can_return_current_stats_query_builder(): void
    {
        $resource = new VehicleStatsResource($this->samsara);
        $query = $resource->current();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_return_feed_query_builder(): void
    {
        $resource = new VehicleStatsResource($this->samsara);
        $query = $resource->feed();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_return_history_query_builder(): void
    {
        $resource = new VehicleStatsResource($this->samsara);
        $query = $resource->history();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_return_query_builder(): void
    {
        $resource = new VehicleStatsResource($this->samsara);
        $query = $resource->query();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_feed_hits_correct_endpoint(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [[
                    'id'           => 'vehicle-2',
                    'name'         => 'Truck 2',
                    'fuelPercents' => [[
                        'value'       => 75,
                        'time'        => '2026-08-10T15:00:00Z',
                        'decorations' => ['gps' => ['value' => ['latitude' => 35.8456, 'longitude' => -86.3903]]],
                    ]],
                ]],
                'pagination' => ['endCursor' => '', 'hasNextPage' => false],
            ]),
        ]);

        $resource = new VehicleStatsResource($this->samsara);
        $page = $resource->feed()
            ->types(['fuelPercents', 'gps'])
            ->withDecorations('gps')
            ->after('stats-feed-cursor')
            ->paginate();

        $stats = $page->items();
        $this->assertInstanceOf(EntityCollection::class, $stats);
        $this->assertCount(1, $stats);
        $this->assertInstanceOf(VehicleStatsSeries::class, $stats->first());
        $this->assertSame(75, $stats->first()->getFuelPercentReadings()->first()->value);
        $this->assertSame(35.8456, $stats->first()->getFuelPercentReadings()->first()->decorations['gps']['value']['latitude']);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/vehicles/stats/feed'
            && $request->data() === [
                'after'       => 'stats-feed-cursor',
                'types'       => 'fuelPercents,gps',
                'decorations' => 'gps',
            ]
        );
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new VehicleStatsResource($this->samsara);

        $this->assertSame('/fleet/vehicles/stats', $resource->getEndpoint());
    }

    #[Test]
    public function it_has_correct_entity_class(): void
    {
        $resource = new VehicleStatsResource($this->samsara);

        $this->assertSame(VehicleStats::class, $resource->getEntityClass());
    }

    #[Test]
    public function it_history_hits_correct_endpoint(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [[
                    'id'   => 'vehicle-1',
                    'name' => 'Truck 1',
                    'gps'  => [[
                        'latitude'  => 37.7749,
                        'longitude' => -122.4194,
                        'time'      => '2026-08-10T14:30:00Z',
                    ]],
                ]],
                'pagination' => ['endCursor' => '', 'hasNextPage' => false],
            ]),
        ]);

        $resource = new VehicleStatsResource($this->samsara);
        $page = $resource->history()
            ->between('2026-08-10T14:00:00Z', '2026-08-10T15:00:00Z')
            ->types('gps')
            ->paginate();

        $stats = $page->items();
        $this->assertInstanceOf(EntityCollection::class, $stats);
        $this->assertCount(1, $stats);
        $this->assertInstanceOf(VehicleStatsSeries::class, $stats->first());
        $this->assertSame('vehicle-1', $stats->first()->getId());
        $this->assertSame(-122.4194, $stats->first()->getGpsReadings()->first()->longitude);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/vehicles/stats/history'
            && $request->data() === [
                'startTime' => '2026-08-10T14:00:00Z',
                'endTime'   => '2026-08-10T15:00:00Z',
                'types'     => 'gps',
            ]
        );
    }

    #[Test]
    public function it_terminal_executes_current_stats_with_required_types(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [[
                    'id'          => 'vehicle-1',
                    'name'        => 'Truck 1',
                    'fuelPercent' => ['value' => 75, 'time' => '2026-08-10T15:00:00Z'],
                ]],
                'pagination' => ['endCursor' => '', 'hasNextPage' => false],
            ]),
        ]);

        $page = (new VehicleStatsResource($this->samsara))
            ->current()
            ->types('fuelPercents')
            ->whereVehicle(['vehicle-1', 'vehicle-2'])
            ->paginate();

        $this->assertInstanceOf(VehicleStats::class, $page->items()->first());
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/vehicles/stats'
            && $request->data() === [
                'vehicleIds' => 'vehicle-1,vehicle-2',
                'types'      => 'fuelPercents',
            ]
        );
    }

    #[Test]
    public function it_terminal_executes_the_base_stats_query(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [[
                    'id'   => 'vehicle-1',
                    'name' => 'Truck 1',
                    'gps'  => ['latitude' => 35.8, 'longitude' => -86.3, 'time' => '2026-08-10T15:00:00Z'],
                ]],
                'pagination' => ['endCursor' => '', 'hasNextPage' => false],
            ]),
        ]);

        $stats = (new VehicleStatsResource($this->samsara))
            ->query()
            ->types('gps')
            ->get();

        $this->assertInstanceOf(VehicleStats::class, $stats->first());
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/vehicles/stats'
            && $request->data() === ['types' => 'gps']
        );
    }
}
