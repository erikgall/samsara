<?php

namespace Samsara\Tests\Unit\Resources\Fleet;

use Samsara\Samsara;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use Samsara\Data\EntityCollection;
use Illuminate\Http\Client\Request;
use Samsara\Data\Equipment\Equipment;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\Equipment\EquipmentStats;
use Samsara\Resources\Fleet\EquipmentResource;
use Samsara\Data\Equipment\EquipmentStatsSeries;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Data\Equipment\EquipmentLocationResult;
use Samsara\Data\Equipment\EquipmentLocationSeries;
use Samsara\Exceptions\UnsupportedOperationException;

/**
 * Unit tests for the EquipmentResource.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class EquipmentResourceTest extends TestCase
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
        $resource = new EquipmentResource($this->samsara);

        $this->assertInstanceOf(EquipmentResource::class, $resource);
    }

    #[Test]
    public function it_can_create_equipment(): void
    {
        $resource = new EquipmentResource($this->samsara);

        try {
            $resource->create(['name' => 'New Equipment']);
            $this->fail('Equipment create must fail before sending HTTP.');
        } catch (UnsupportedOperationException $exception) {
            $this->assertSame('create', $exception->getContext()['operation'] ?? null);
        }

        $this->http->assertNothingSent();
    }

    #[Test]
    public function it_can_delete_equipment(): void
    {
        $resource = new EquipmentResource($this->samsara);

        try {
            $resource->delete('123');
            $this->fail('Equipment delete must fail before sending HTTP.');
        } catch (UnsupportedOperationException $exception) {
            $this->assertSame('delete', $exception->getContext()['operation'] ?? null);
        }

        $this->http->assertNothingSent();
    }

    #[Test]
    public function it_can_find_equipment_by_external_id(): void
    {
        $this->http->fake([
            '*' => $this->http->response(['data' => []]),
        ]);

        $resource = new EquipmentResource($this->samsara);

        try {
            $resource->findByExternalId('asset_code', 'EQ-123');
            $this->fail('Equipment external-ID lookup must fail before sending HTTP.');
        } catch (UnsupportedOperationException $exception) {
            $this->assertSame(
                'Equipment cannot be looked up by external ID in the current API contract. Use find() with a Samsara equipment ID.',
                $exception->getMessage()
            );
        }

        $this->http->assertNothingSent();
    }

    #[Test]
    public function it_can_find_equipment_by_id(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => ['id' => '123', 'name' => 'Equipment 001'],
            ]),
        ]);

        $resource = new EquipmentResource($this->samsara);
        $equipment = $resource->find('123');

        $this->assertInstanceOf(Equipment::class, $equipment);
        $this->assertSame('123', $equipment->id);
        $this->assertSame('Equipment 001', $equipment->name);
    }

    #[Test]
    public function it_can_get_all_equipment(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    ['id' => '1', 'name' => 'Equipment 001'],
                    ['id' => '2', 'name' => 'Equipment 002'],
                ],
            ]),
        ]);

        $resource = new EquipmentResource($this->samsara);
        $equipment = $resource->all();

        $this->assertInstanceOf(EntityCollection::class, $equipment);
        $this->assertCount(2, $equipment);
        $this->assertInstanceOf(Equipment::class, $equipment->first());
        $this->assertSame('Equipment 001', $equipment->first()->name);
    }

    #[Test]
    public function it_can_return_locations_feed_query_builder(): void
    {
        $resource = new EquipmentResource($this->samsara);
        $query = $resource->locationsFeed();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_return_locations_history_query_builder(): void
    {
        $resource = new EquipmentResource($this->samsara);
        $query = $resource->locationsHistory();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_return_locations_query_builder(): void
    {
        $resource = new EquipmentResource($this->samsara);
        $query = $resource->locations();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_return_stats_feed_query_builder(): void
    {
        $resource = new EquipmentResource($this->samsara);
        $query = $resource->statsFeed();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_return_stats_history_query_builder(): void
    {
        $resource = new EquipmentResource($this->samsara);
        $query = $resource->statsHistory();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_return_stats_query_builder(): void
    {
        $resource = new EquipmentResource($this->samsara);
        $query = $resource->stats();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_update_equipment(): void
    {
        $resource = new EquipmentResource($this->samsara);

        try {
            $resource->update('123', ['name' => 'Updated Equipment']);
            $this->fail('Equipment update must fail before sending HTTP.');
        } catch (UnsupportedOperationException $exception) {
            $this->assertSame('update', $exception->getContext()['operation'] ?? null);
        }

        $this->http->assertNothingSent();
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new EquipmentResource($this->samsara);

        $this->assertSame('/fleet/equipment', $resource->getEndpoint());
    }

    #[Test]
    public function it_has_correct_entity_class(): void
    {
        $resource = new EquipmentResource($this->samsara);

        $this->assertSame(Equipment::class, $resource->getEntityClass());
    }

    #[Test]
    public function it_maps_equipment_location_feed_series_with_exact_pagination_request(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [[
                    'id'        => 'equipment-1',
                    'name'      => 'Excavator',
                    'locations' => [[
                        'latitude'  => 35.8456,
                        'longitude' => -86.3903,
                        'time'      => '2026-08-10T15:00:00Z',
                    ]],
                ]],
                'pagination' => ['endCursor' => '', 'hasNextPage' => false],
            ]),
        ]);

        $page = (new EquipmentResource($this->samsara))
            ->locationsFeed()
            ->where('equipmentIds', 'equipment-1,equipment-2')
            ->after('feed-cursor')
            ->paginate();

        $series = $page->items()->first();
        $this->assertSame(EquipmentLocationSeries::class, $series::class);
        $this->assertCount(1, $series->getLocations());
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/equipment/locations/feed'
            && $request->data() === [
                'equipmentIds' => 'equipment-1,equipment-2',
                'after'        => 'feed-cursor',
            ]
        );
    }

    #[Test]
    public function it_maps_equipment_location_history_series_with_required_times(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [[
                    'id'        => 'equipment-1',
                    'name'      => 'Excavator',
                    'locations' => [[
                        'latitude'  => 35.8456,
                        'longitude' => -86.3903,
                        'time'      => '2026-08-10T14:30:00Z',
                    ]],
                ]],
                'pagination' => ['endCursor' => '', 'hasNextPage' => false],
            ]),
        ]);

        $page = (new EquipmentResource($this->samsara))
            ->locationsHistory()
            ->between('2026-08-10T14:00:00Z', '2026-08-10T15:00:00Z')
            ->where('equipmentIds', 'equipment-1')
            ->paginate();

        $series = $page->items()->first();
        $this->assertSame(EquipmentLocationSeries::class, $series::class);
        $this->assertSame(35.8456, $series->getLocations()->first()->latitude);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/equipment/locations/history'
            && $request->data() === [
                'startTime'    => '2026-08-10T14:00:00Z',
                'endTime'      => '2026-08-10T15:00:00Z',
                'equipmentIds' => 'equipment-1',
            ]
        );
    }

    #[Test]
    public function it_maps_equipment_stats_feed_with_exact_request(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [[
                    'id'        => 'equipment-1',
                    'name'      => 'Excavator',
                    'engineRpm' => [['value' => 1800, 'time' => '2026-08-10T15:00:00Z']],
                    'gps'       => [[
                        'latitude'  => 35.8456,
                        'longitude' => -86.3903,
                        'time'      => '2026-08-10T15:00:00Z',
                        'address'   => ['id' => 'address-1', 'name' => 'Main Yard'],
                    ]],
                ]],
                'pagination' => ['endCursor' => '', 'hasNextPage' => false],
            ]),
        ]);

        $page = (new EquipmentResource($this->samsara))
            ->statsFeed()
            ->types(['engineRpm', 'gps'])
            ->after('stats-cursor')
            ->paginate();

        $stats = $page->items()->first();
        $this->assertSame(EquipmentStatsSeries::class, $stats::class);
        $this->assertSame(1800, $stats->getEngineRpmReadings()->first()->value);
        $this->assertSame('Main Yard', $stats->getGpsReadings()->first()->address['name']);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/equipment/stats/feed'
            && $request->data() === [
                'after' => 'stats-cursor',
                'types' => 'engineRpm,gps',
            ]
        );
    }

    #[Test]
    public function it_maps_equipment_stats_history_with_required_times(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [[
                    'id'           => 'equipment-1',
                    'name'         => 'Excavator',
                    'fuelPercents' => [['value' => 75, 'time' => '2026-08-10T14:30:00Z']],
                ]],
                'pagination' => ['endCursor' => '', 'hasNextPage' => false],
            ]),
        ]);

        $page = (new EquipmentResource($this->samsara))
            ->statsHistory()
            ->between('2026-08-10T14:00:00Z', '2026-08-10T15:00:00Z')
            ->types('fuelPercents')
            ->paginate();

        $stats = $page->items()->first();
        $this->assertSame(EquipmentStatsSeries::class, $stats::class);
        $this->assertSame(75, $stats->getFuelPercentReadings()->first()->value);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/equipment/stats/history'
            && $request->data() === [
                'startTime' => '2026-08-10T14:00:00Z',
                'endTime'   => '2026-08-10T15:00:00Z',
                'types'     => 'fuelPercents',
            ]
        );
    }

    #[Test]
    public function it_maps_equipment_stats_snapshot_with_required_types(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [[
                    'id'        => 'equipment-1',
                    'name'      => 'Excavator',
                    'engineRpm' => ['value' => 1800, 'time' => '2026-08-10T15:00:00Z'],
                ]],
                'pagination' => ['endCursor' => '', 'hasNextPage' => false],
            ]),
        ]);

        $page = (new EquipmentResource($this->samsara))
            ->stats()
            ->types('engineRpm')
            ->paginate();

        $stats = $page->items()->first();
        $this->assertSame(EquipmentStats::class, $stats::class);
        $this->assertSame(1800, $stats->getEngineRpm());
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/equipment/stats'
            && $request->data() === ['types' => 'engineRpm']
        );
    }

    #[Test]
    public function it_maps_specialized_equipment_results_and_omits_unsupported_page_size(): void
    {
        $this->http->fakeSequence()
            ->push([
                'data' => [[
                    'id'       => 'equipment-1',
                    'name'     => 'Excavator',
                    'location' => [
                        'latitude'  => 35.8456,
                        'longitude' => -86.3903,
                        'time'      => '2026-08-10T15:00:00Z',
                        'heading'   => 180.0,
                        'speed'     => 12.5,
                    ],
                ]],
                'pagination' => ['endCursor' => 'equipment-next', 'hasNextPage' => true],
            ])
            ->push([
                'data'       => [],
                'pagination' => ['endCursor' => '', 'hasNextPage' => false],
            ]);

        $page = (new EquipmentResource($this->samsara))
            ->locations()
            ->where('equipmentIds', 'equipment-1')
            ->paginate();
        $nextPage = $page->nextPage();

        $result = $page->items()->first();
        $this->assertSame(EquipmentLocationResult::class, $result::class);
        $this->assertSame(12.5, $result->getLocation()->speed);
        $this->assertNotNull($nextPage);
        $this->http->assertSentCount(2);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/equipment/locations'
            && $request->data() === ['equipmentIds' => 'equipment-1']
        );
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/equipment/locations'
            && $request->data() === ['equipmentIds' => 'equipment-1', 'after' => 'equipment-next']
        );
    }

    #[Test]
    public function it_returns_null_when_equipment_not_found(): void
    {
        $this->http->fake([
            '*' => $this->http->response([], 404),
        ]);

        $resource = new EquipmentResource($this->samsara);
        $equipment = $resource->find('999');

        $this->assertNull($equipment);
    }

    #[Test]
    public function it_terminal_executes_the_current_equipment_list_query(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data'       => [['id' => 'equipment-1', 'name' => 'Loader']],
                'pagination' => ['endCursor' => '', 'hasNextPage' => false],
            ]),
        ]);

        $page = (new EquipmentResource($this->samsara))
            ->query()
            ->whereParentTag(['parent-1', 'parent-2'])
            ->whereTag(['tag-1', 'tag-2'])
            ->after('equipment-cursor')
            ->paginate(50);

        $this->assertInstanceOf(Equipment::class, $page->items()->first());
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/equipment'
            && $request->data() === [
                'parentTagIds' => 'parent-1,parent-2',
                'tagIds'       => 'tag-1,tag-2',
                'limit'        => 50,
                'after'        => 'equipment-cursor',
            ]
        );
    }
}
