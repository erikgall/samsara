<?php

namespace Samsara\Tests\Unit\Resources\Safety;

use Samsara\Samsara;
use Samsara\Tests\TestCase;
use Samsara\Data\EntityCollection;
use Samsara\Data\Maintenance\Dvir;
use Illuminate\Http\Client\Request;
use Samsara\Data\Maintenance\Defect;
use Samsara\Data\Safety\SafetyEvent;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\HoursOfService\HosLog;
use Samsara\Data\HoursOfService\HosDailyLog;
use Samsara\Data\Safety\SafetyEventActivity;
use Samsara\Data\HoursOfService\HosViolation;
use Samsara\Data\HoursOfService\HosLogsForDriver;
use Samsara\Resources\Safety\MaintenanceResource;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Data\HoursOfService\HosViolationGroup;
use Samsara\Resources\Safety\SafetyEventsResource;
use Samsara\Data\HoursOfService\HosClocksForDriver;
use Samsara\Resources\Safety\HoursOfServiceResource;

/**
 * Current first-party contract tests for owned Safety resources.
 */
class CurrentContractResourceTest extends TestCase
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
    public function it_cursor_endpoints_without_limit_do_not_receive_an_automatic_page_size(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data'       => [],
                'pagination' => ['endCursor' => null, 'hasNextPage' => false],
            ]),
        ]);

        $resources = [
            (new HoursOfServiceResource($this->samsara))->dailyLogs(),
            (new HoursOfServiceResource($this->samsara))->logs(),
            (new HoursOfServiceResource($this->samsara))->violations(),
            (new SafetyEventsResource($this->samsara))->query(),
            (new SafetyEventsResource($this->samsara))->auditLogs(),
        ];

        foreach ($resources as $builder) {
            $builder->paginate();
        }

        $this->http->assertNotSent(fn (Request $request): bool => array_key_exists('limit', $request->data()));
        $this->http->assertSentCount(count($resources));
    }

    #[Test]
    public function it_maps_hos_clocks_without_losing_the_official_driver_wrapper(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [[
                    'driver' => ['id' => 'driver-1', 'name' => 'A. Driver'],
                    'clocks' => ['drive' => ['driveRemainingDurationMs' => 3_600_000]],
                ]],
                'pagination' => ['hasNextPage' => false],
            ]),
        ]);

        $result = (new HoursOfServiceResource($this->samsara))
            ->clocks()
            ->whereDriver(['driver-1', 'driver-2'])
            ->get();

        $clock = $result->first();
        $this->assertInstanceOf(HosClocksForDriver::class, $clock);
        $this->assertSame('driver-1', $clock->driver['id']);
        $this->assertSame(1.0, $clock->getClocks()->getDriveRemainingHours());
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && $request->url() === 'https://api.samsara.com/fleet/hos/clocks?driverIds=driver-1%2Cdriver-2');
    }

    #[Test]
    public function it_maps_hos_daily_logs_to_their_endpoint_specific_entity(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [[
                    'driver'    => ['id' => 'driver-1'],
                    'startTime' => '2026-08-01T00:00:00Z',
                    'endTime'   => '2026-08-02T00:00:00Z',
                ]],
                'pagination' => ['hasNextPage' => false],
            ]),
        ]);

        $result = (new HoursOfServiceResource($this->samsara))
            ->dailyLogs()
            ->where('startDate', '2026-08-01')
            ->get();

        $this->assertInstanceOf(HosDailyLog::class, $result->first());
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && $request->url() === 'https://api.samsara.com/fleet/hos/daily-logs?startDate=2026-08-01');
    }

    #[Test]
    public function it_maps_hos_logs_without_losing_the_official_driver_wrapper(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [[
                    'driver'  => ['id' => 'driver-1'],
                    'hosLogs' => [['hosStatusType' => 'driving']],
                ]],
                'pagination' => ['hasNextPage' => false],
            ]),
        ]);

        $result = (new HoursOfServiceResource($this->samsara))
            ->logs()
            ->whereDriver('driver-1')
            ->get();

        $log = $result->first();
        $this->assertInstanceOf(HosLogsForDriver::class, $log);
        $this->assertSame('driver-1', $log->driver['id']);
        $this->assertInstanceOf(HosLog::class, $log->getLogs()->first());
        $this->assertSame('driving', $log->getLogs()->first()->hosStatusType);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && $request->url() === 'https://api.samsara.com/fleet/hos/logs?driverIds=driver-1');
    }

    #[Test]
    public function it_maps_hos_violation_wrappers_without_dropping_nested_violations(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [[
                    'violations' => [[
                        'driver'     => ['id' => 'driver-1'],
                        'type'       => 'shiftHours',
                        'durationMs' => 60_000,
                    ]],
                ]],
                'pagination' => ['hasNextPage' => false],
            ]),
        ]);

        $result = (new HoursOfServiceResource($this->samsara))
            ->violations()
            ->whereDriver('driver-1')
            ->get();

        $violation = $result->first();
        $this->assertInstanceOf(HosViolationGroup::class, $violation);
        $this->assertInstanceOf(HosViolation::class, $violation->getViolations()->first());
        $this->assertSame('shiftHours', $violation->getViolations()->first()->type);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && $request->url() === 'https://api.samsara.com/fleet/hos/violations?driverIds=driver-1');
    }

    #[Test]
    public function it_maps_safety_activity_feed_items_as_generic_entities(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [[
                    'id'          => 'activity-1',
                    'time'        => '2026-08-01T00:00:00Z',
                    'type'        => 'coachingStateUpdated',
                    'safetyEvent' => ['id' => 'event-1'],
                ]],
                'pagination' => ['hasNextPage' => false],
            ]),
        ]);

        $result = (new SafetyEventsResource($this->samsara))->auditLogs()->get();

        $activity = $result->first();
        $this->assertInstanceOf(SafetyEventActivity::class, $activity);
        $this->assertInstanceOf(SafetyEvent::class, $activity->getSafetyEvent());
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && $request->url() === 'https://api.samsara.com/fleet/safety-events/audit-logs/feed');
    }

    #[Test]
    public function it_maps_the_defects_stream_to_defect_entities(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data'       => [['id' => 'defect-1', 'comment' => 'Engine failure']],
                'pagination' => ['hasNextPage' => false],
            ]),
        ]);

        $result = (new MaintenanceResource($this->samsara))
            ->defects()
            ->where('startTime', '2026-08-01T00:00:00Z')
            ->get();

        $this->assertInstanceOf(EntityCollection::class, $result);
        $this->assertInstanceOf(Defect::class, $result->first());
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && $request->url() === 'https://api.samsara.com/defects/stream?startTime=2026-08-01T00%3A00%3A00Z');
    }

    #[Test]
    public function it_maps_the_dvir_stream_to_dvir_entities(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data'       => [['id' => 'dvir-1', 'type' => 'preTrip']],
                'pagination' => ['hasNextPage' => false],
            ]),
        ]);

        $result = (new MaintenanceResource($this->samsara))
            ->dvirs()
            ->where('startTime', '2026-08-01T00:00:00Z')
            ->where('safetyStatus', ['safe', 'unsafe'])
            ->get();

        $this->assertInstanceOf(Dvir::class, $result->first());
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && $request->url() === 'https://api.samsara.com/dvirs/stream?safetyStatus=safe&safetyStatus=unsafe&startTime=2026-08-01T00%3A00%3A00Z');
    }

    #[Test]
    public function it_posts_dvirs_to_the_current_fleet_endpoint(): void
    {
        $payload = [
            'authorId'     => 'author-123',
            'safetyStatus' => 'safe',
            'type'         => 'mechanic',
            'vehicleId'    => 'vehicle-123',
        ];
        $this->http->fake([
            '*' => $this->http->response(['data' => ['id' => 'dvir-1', ...$payload]], 200),
        ]);

        $result = (new MaintenanceResource($this->samsara))->createDvir($payload);

        $this->assertInstanceOf(Dvir::class, $result);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'POST'
            && $request->url() === 'https://api.samsara.com/fleet/dvirs'
            && $request->data() === $payload);
    }

    #[Test]
    public function it_uses_the_legacy_safety_event_contract_for_the_existing_query_surface(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data'       => [['id' => 'event-1', 'time' => '2026-08-01T00:00:00Z']],
                'pagination' => ['hasNextPage' => false],
            ]),
        ]);

        $result = (new SafetyEventsResource($this->samsara))
            ->query()
            ->between('2026-08-01T00:00:00Z', '2026-08-02T00:00:00Z')
            ->get();

        $this->assertInstanceOf(SafetyEvent::class, $result->first());
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && $request->url() === 'https://api.samsara.com/fleet/safety-events?startTime=2026-08-01T00%3A00%3A00Z&endTime=2026-08-02T00%3A00%3A00Z');
    }
}
