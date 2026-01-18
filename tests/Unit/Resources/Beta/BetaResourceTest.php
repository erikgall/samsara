<?php

namespace ErikGall\Samsara\Tests\Unit\Resources\Beta;

use ErikGall\Samsara\Samsara;
use ErikGall\Samsara\Query\Builder;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Data\EntityCollection;
use ErikGall\Samsara\Resources\Beta\BetaResource;
use Illuminate\Http\Client\Factory as HttpFactory;

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
    public function it_can_create_industrial_job(): void
    {
        $this->http->fake([
            '*/industrial/jobs' => $this->http->response([
                'data' => [
                    'id' => 'job-123',
                ],
            ], 201),
        ]);

        $resource = new BetaResource($this->samsara);

        $result = $resource->createIndustrialJob(['name' => 'Test Job']);

        $this->assertIsArray($result);
        $this->assertSame('job-123', $result['id']);
    }

    #[Test]
    public function it_can_create_reading(): void
    {
        $this->http->fake([
            '*/readings' => $this->http->response([
                'data' => [
                    'id' => 'reading-123',
                ],
            ], 201),
        ]);

        $resource = new BetaResource($this->samsara);

        $result = $resource->createReading(['definitionId' => 'def-1', 'value' => 100]);

        $this->assertIsArray($result);
        $this->assertSame('reading-123', $result['id']);
    }

    #[Test]
    public function it_can_delete_industrial_job(): void
    {
        $this->http->fake([
            '*/industrial/jobs/job-123' => $this->http->response([], 204),
        ]);

        $resource = new BetaResource($this->samsara);

        $result = $resource->deleteIndustrialJob('job-123');

        $this->assertTrue($result);
    }

    #[Test]
    public function it_can_get_aemp_fleet(): void
    {
        $resource = new BetaResource($this->samsara);

        $builder = $resource->aempFleet();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_get_detections_stream(): void
    {
        $resource = new BetaResource($this->samsara);

        $builder = $resource->detectionsStream();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_get_devices(): void
    {
        $this->http->fake([
            '*/devices' => $this->http->response([
                'data' => [
                    ['id' => 'device-1'],
                    ['id' => 'device-2'],
                ],
            ], 200),
        ]);

        $resource = new BetaResource($this->samsara);

        $result = $resource->devices();

        $this->assertInstanceOf(EntityCollection::class, $result);
        $this->assertCount(2, $result);
    }

    #[Test]
    public function it_can_get_hos_eld_events(): void
    {
        $resource = new BetaResource($this->samsara);

        $builder = $resource->hosEldEvents();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_get_industrial_jobs(): void
    {
        $resource = new BetaResource($this->samsara);

        $builder = $resource->industrialJobs();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_get_qualifications_records(): void
    {
        $resource = new BetaResource($this->samsara);

        $builder = $resource->qualificationsRecords();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_get_qualifications_types(): void
    {
        $this->http->fake([
            '*/qualifications/types' => $this->http->response([
                'data' => [
                    ['id' => 'type-1'],
                    ['id' => 'type-2'],
                ],
            ], 200),
        ]);

        $resource = new BetaResource($this->samsara);

        $result = $resource->qualificationsTypes();

        $this->assertInstanceOf(EntityCollection::class, $result);
        $this->assertCount(2, $result);
    }

    #[Test]
    public function it_can_get_readings_definitions(): void
    {
        $this->http->fake([
            '*/readings/definitions' => $this->http->response([
                'data' => [
                    ['id' => 'def-1'],
                    ['id' => 'def-2'],
                ],
            ], 200),
        ]);

        $resource = new BetaResource($this->samsara);

        $result = $resource->readingsDefinitions();

        $this->assertInstanceOf(EntityCollection::class, $result);
        $this->assertCount(2, $result);
    }

    #[Test]
    public function it_can_get_readings_history(): void
    {
        $resource = new BetaResource($this->samsara);

        $builder = $resource->readingsHistory();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_get_readings_latest(): void
    {
        $resource = new BetaResource($this->samsara);

        $builder = $resource->readingsLatest();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_get_reports_configs(): void
    {
        $this->http->fake([
            '*/reports/configs' => $this->http->response([
                'data' => [
                    ['id' => 'config-1'],
                    ['id' => 'config-2'],
                ],
            ], 200),
        ]);

        $resource = new BetaResource($this->samsara);

        $result = $resource->reportsConfigs();

        $this->assertInstanceOf(EntityCollection::class, $result);
        $this->assertCount(2, $result);
    }

    #[Test]
    public function it_can_get_reports_datasets(): void
    {
        $resource = new BetaResource($this->samsara);

        $builder = $resource->reportsDatasets();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_get_reports_runs(): void
    {
        $resource = new BetaResource($this->samsara);

        $builder = $resource->reportsRuns();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_get_safety_scores_drivers(): void
    {
        $resource = new BetaResource($this->samsara);

        $builder = $resource->safetyScoresDrivers();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_get_safety_scores_vehicles(): void
    {
        $resource = new BetaResource($this->samsara);

        $builder = $resource->safetyScoresVehicles();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_get_trailer_stats_current(): void
    {
        $resource = new BetaResource($this->samsara);

        $builder = $resource->trailerStatsCurrent();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_get_trailer_stats_feed(): void
    {
        $resource = new BetaResource($this->samsara);

        $builder = $resource->trailerStatsFeed();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_get_trailer_stats_history(): void
    {
        $resource = new BetaResource($this->samsara);

        $builder = $resource->trailerStatsHistory();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_get_training_assignments(): void
    {
        $resource = new BetaResource($this->samsara);

        $builder = $resource->trainingAssignments();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_get_training_courses(): void
    {
        $this->http->fake([
            '*/training/courses' => $this->http->response([
                'data' => [
                    ['id' => 'course-1'],
                    ['id' => 'course-2'],
                ],
            ], 200),
        ]);

        $resource = new BetaResource($this->samsara);

        $result = $resource->trainingCourses();

        $this->assertInstanceOf(EntityCollection::class, $result);
        $this->assertCount(2, $result);
    }

    #[Test]
    public function it_can_get_vehicle_immobilizer_stream(): void
    {
        $resource = new BetaResource($this->samsara);

        $builder = $resource->vehicleImmobilizerStream();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_update_industrial_job(): void
    {
        $this->http->fake([
            '*/industrial/jobs/job-123' => $this->http->response([
                'data' => [
                    'id'   => 'job-123',
                    'name' => 'Updated Job',
                ],
            ], 200),
        ]);

        $resource = new BetaResource($this->samsara);

        $result = $resource->updateIndustrialJob('job-123', ['name' => 'Updated Job']);

        $this->assertIsArray($result);
        $this->assertSame('Updated Job', $result['name']);
    }
}
