<?php

namespace Samsara\Tests\Unit\Resources\Beta;

use Samsara\Samsara;
use Samsara\Data\Entity;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use Samsara\Data\Safety\SafetyScore;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\Trailer\TrailerStats;
use Samsara\Resources\Beta\BetaResource;
use PHPUnit\Framework\Attributes\DataProvider;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Exceptions\UnsupportedOperationException;

/**
 * Unit tests for the BetaResource class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class BetaResourceTest extends TestCase
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
    public function it_creates_an_industrial_job_using_the_beta_collection(): void
    {
        $body = [
            'job' => [
                'id'        => 'job-123',
                'name'      => 'Test Job',
                'startDate' => '2026-08-10',
                'endDate'   => '2026-08-11',
            ],
        ];
        $this->http->fake([
            '*' => $this->http->response(['data' => $body['job']]),
        ]);
        $resource = new BetaResource($this->samsara);

        $result = $resource->createIndustrialJob($body);

        $this->assertSame('job-123', $result['id']);
        $this->http->assertSent(fn ($request): bool => $request->method() === 'POST'
            && parse_url($request->url(), PHP_URL_PATH) === '/beta/industrial/jobs'
            && $request->data() === $body);
    }

    #[Test]
    public function it_creates_readings_without_inventing_a_response_body(): void
    {
        $body = [
            'data' => [[
                'readingId' => 'reading-1',
                'entityId'  => 'asset-1',
                'time'      => '2026-08-10T12:00:00Z',
                'value'     => 100,
            ]],
        ];
        $this->http->fake([
            '*' => $this->http->response(null, 201),
        ]);
        $resource = new BetaResource($this->samsara);

        $result = $resource->createReading($body);

        $this->assertTrue($result);
        $this->http->assertSent(fn ($request): bool => $request->method() === 'POST'
            && parse_url($request->url(), PHP_URL_PATH) === '/readings'
            && $request->data() === $body);
    }

    #[Test]
    public function it_deletes_an_industrial_job_using_the_required_query_id(): void
    {
        $this->http->fake([
            '*' => $this->http->response(['id' => 'job-123', 'uuid' => 'job-uuid']),
        ]);
        $resource = new BetaResource($this->samsara);

        $result = $resource->deleteIndustrialJob('job-123');

        $this->assertSame('job-123', $result['id']);
        $this->http->assertSent(fn ($request): bool => $request->method() === 'DELETE'
            && parse_url($request->url(), PHP_URL_PATH) === '/beta/industrial/jobs'
            && $this->queryFor($request->url()) === ['id' => 'job-123']);
    }

    /**
     * @param  class-string<Entity>  $expectedClass
     * @param  array<string, string>  $expectedQuery
     */
    #[Test]
    #[DataProvider('paginatedBuilderContracts')]
    public function it_executes_paginated_builder_contracts(
        string $method,
        string $path,
        string $expectedClass,
        array $expectedQuery
    ): void {
        $item = match ($expectedClass) {
            TrailerStats::class => [
                'id'                => 'trailer-1',
                'gpsOdometerMeters' => ['value' => 1234],
            ],
            SafetyScore::class => [
                'driverId'    => 'driver-1',
                'driverScore' => 91,
            ],
            default => ['id' => 'entity-1'],
        };
        $this->http->fake([
            '*' => $this->http->response([
                'data'       => [$item],
                'pagination' => ['hasNextPage' => false],
            ]),
        ]);
        $resource = new BetaResource($this->samsara);

        $builder = $this->configureBuilder($resource, $method);
        $result = $builder->after('cursor-1')->paginate();

        $entity = $result->items()->first();
        $this->assertSame($expectedClass, $entity::class);
        $this->http->assertSent(fn ($request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === $path
            && $this->queryFor($request->url()) === $expectedQuery);
    }

    #[Test]
    public function it_gets_aemp_fleet_from_the_current_page_path(): void
    {
        $this->http->fake([
            '*' => $this->http->response(['Fleet' => ['links' => []]]),
        ]);
        $resource = new BetaResource($this->samsara);

        $result = $resource->aempFleet('7');

        $this->assertSame(['Fleet' => ['links' => []]], $result);
        $this->http->assertSent(fn ($request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/beta/aemp/Fleet/7');
    }

    #[Test]
    public function it_rejects_phantom_generic_operations_on_the_beta_root(): void
    {
        $this->http->fake();
        $resource = new BetaResource($this->samsara);

        try {
            $resource->all();
            $this->fail('Expected the inherited all operation to be rejected.');
        } catch (UnsupportedOperationException) {
            $this->http->assertNothingSent();
        }
    }

    #[Test]
    public function it_updates_an_industrial_job_using_the_required_query_id(): void
    {
        $body = [
            'job' => [
                'id'        => 'job-123',
                'name'      => 'Updated Job',
                'startDate' => '2026-08-10',
                'endDate'   => '2026-08-11',
            ],
            'keepHistory' => true,
        ];
        $this->http->fake([
            '*' => $this->http->response(['data' => $body['job']]),
        ]);
        $resource = new BetaResource($this->samsara);

        $result = $resource->updateIndustrialJob('job-123', $body);

        $this->assertSame('Updated Job', $result['name']);
        $this->http->assertSent(fn ($request): bool => $request->method() === 'PATCH'
            && parse_url($request->url(), PHP_URL_PATH) === '/beta/industrial/jobs'
            && $this->queryFor($request->url()) === ['id' => 'job-123']
            && $request->data() === $body);
    }

    #[Test]
    public function it_uses_the_current_qualification_records_path_and_serialization(): void
    {
        $this->http->fake([
            '*' => $this->http->response(['data' => [['id' => 'record-1']]]),
        ]);
        $resource = new BetaResource($this->samsara);

        $result = $resource->qualificationsRecords()->where('ids', 'record-1,record-2')->get();

        $this->assertSame(Entity::class, $result->first()::class);
        $this->http->assertSent(fn ($request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/qualification-records'
            && $this->queryFor($request->url()) === ['ids' => 'record-1,record-2']);
    }

    /**
     * @return array<string, array{string, string, class-string<Entity>, array<string, string>}>
     */
    public static function paginatedBuilderContracts(): array
    {
        return [
            'detections' => [
                'detectionsStream',
                '/detections/stream',
                Entity::class,
                [
                    'startTime' => '2026-08-10T12:00:00Z',
                    'driverIds' => 'driver-1,driver-2',
                    'after'     => 'cursor-1',
                ],
            ],
            'devices'        => ['devices', '/devices', Entity::class, ['limit' => '100', 'after' => 'cursor-1']],
            'hos eld events' => [
                'hosEldEvents',
                '/beta/fleet/hos/drivers/eld-events',
                Entity::class,
                [
                    'startTime' => '2026-08-10T12:00:00Z',
                    'endTime'   => '2026-08-10T13:00:00Z',
                    'driverIds' => 'driver-1,driver-2',
                    'limit'     => '25',
                    'after'     => 'cursor-1',
                ],
            ],
            'industrial jobs'     => ['industrialJobs', '/beta/industrial/jobs', Entity::class, ['after' => 'cursor-1']],
            'qualification types' => [
                'qualificationsTypes',
                '/qualification-types',
                Entity::class,
                ['entityType' => 'worker', 'after' => 'cursor-1'],
            ],
            'reading definitions' => ['readingsDefinitions', '/readings/definitions', Entity::class, ['after' => 'cursor-1']],
            'reading history'     => [
                'readingsHistory',
                '/readings/history',
                Entity::class,
                ['readingId' => 'reading-1', 'entityType' => 'asset', 'after' => 'cursor-1'],
            ],
            'reading snapshot' => [
                'readingsLatest',
                '/readings/latest',
                Entity::class,
                ['readingIds' => 'reading-1', 'entityType' => 'asset', 'after' => 'cursor-1'],
            ],
            'report configs'       => ['reportsConfigs', '/reports/configs', Entity::class, ['limit' => '100', 'after' => 'cursor-1']],
            'report datasets'      => ['reportsDatasets', '/reports/datasets', Entity::class, ['limit' => '100', 'after' => 'cursor-1']],
            'report runs'          => ['reportsRuns', '/reports/runs', Entity::class, ['limit' => '100', 'after' => 'cursor-1']],
            'driver safety scores' => [
                'safetyScoresDrivers',
                '/safety-scores/drivers',
                SafetyScore::class,
                [
                    'startTime' => '2026-08-10T12:00:00Z',
                    'endTime'   => '2026-08-10T13:00:00Z',
                    'driverIds' => 'driver-1,driver-2',
                    'after'     => 'cursor-1',
                ],
            ],
            'vehicle safety scores' => [
                'safetyScoresVehicles',
                '/safety-scores/vehicles',
                Entity::class,
                [
                    'startTime'  => '2026-08-10T12:00:00Z',
                    'endTime'    => '2026-08-10T13:00:00Z',
                    'vehicleIds' => 'vehicle-1,vehicle-2',
                    'after'      => 'cursor-1',
                ],
            ],
            'trailer stats snapshot' => ['trailerStatsCurrent', '/fleet/trailers/stats', TrailerStats::class, ['after' => 'cursor-1']],
            'trailer stats feed'     => ['trailerStatsFeed', '/fleet/trailers/stats/feed', Entity::class, ['after' => 'cursor-1']],
            'trailer stats history'  => ['trailerStatsHistory', '/fleet/trailers/stats/history', Entity::class, ['after' => 'cursor-1']],
            'training assignments'   => [
                'trainingAssignments',
                '/training-assignments/stream',
                Entity::class,
                ['startTime' => '2026-08-10T12:00:00Z', 'after' => 'cursor-1'],
            ],
            'training courses'    => ['trainingCourses', '/training-courses', Entity::class, ['after' => 'cursor-1']],
            'vehicle immobilizer' => [
                'vehicleImmobilizerStream',
                '/fleet/vehicles/immobilizer/stream',
                Entity::class,
                ['after' => 'cursor-1'],
            ],
        ];
    }

    private function configureBuilder(BetaResource $resource, string $method): Builder
    {
        $builder = $resource->{$method}();

        return match ($method) {
            'detectionsStream' => $builder
                ->startTime('2026-08-10T12:00:00Z')
                ->whereDriver(['driver-1', 'driver-2']),
            'hosEldEvents', 'safetyScoresDrivers' => $builder
                ->between('2026-08-10T12:00:00Z', '2026-08-10T13:00:00Z')
                ->whereDriver(['driver-1', 'driver-2']),
            'safetyScoresVehicles' => $builder
                ->between('2026-08-10T12:00:00Z', '2026-08-10T13:00:00Z')
                ->whereVehicle(['vehicle-1', 'vehicle-2']),
            'qualificationsTypes' => $builder->where('entityType', 'worker'),
            'readingsHistory'     => $builder
                ->where('readingId', 'reading-1')
                ->where('entityType', 'asset'),
            'readingsLatest' => $builder
                ->where('readingIds', 'reading-1')
                ->where('entityType', 'asset'),
            'trainingAssignments' => $builder->startTime('2026-08-10T12:00:00Z'),
            default               => $builder,
        };
    }

    /**
     * @return array<string, string>
     */
    private function queryFor(string $url): array
    {
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        /** @var array<string, string> $query */
        return $query;
    }
}
