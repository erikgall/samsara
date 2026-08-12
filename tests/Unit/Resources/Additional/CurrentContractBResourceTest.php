<?php

namespace Samsara\Tests\Unit\Resources\Additional;

use Samsara\Samsara;
use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use Samsara\Data\Trailer\Trailer;
use Illuminate\Http\Client\Request;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Query\Pagination\CursorPaginator;
use Samsara\Resources\Additional\IftaResource;
use Samsara\Resources\Additional\IdlingResource;
use Samsara\Resources\Additional\IssuesResource;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Resources\Additional\SpeedingResource;
use Samsara\Resources\Additional\TachographResource;
use Samsara\Resources\Additional\FuelAndEnergyResource;
use Samsara\Resources\Additional\TrailerAssignmentsResource;
use Samsara\Resources\Additional\DriverTrailerAssignmentsResource;
use Samsara\Resources\Additional\DriverVehicleAssignmentsResource;
use Samsara\Resources\Additional\CarrierProposedAssignmentsResource;

/**
 * Current first-party contract tests for the second Additional resource group.
 */
class CurrentContractBResourceTest extends TestCase
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
    public function builders_without_an_official_limit_opt_out_of_the_default_page_size(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data'       => [],
                'pagination' => ['hasNextPage' => false],
            ]),
        ]);

        $tachograph = new TachographResource($this->samsara);
        $fuel = new FuelAndEnergyResource($this->samsara);
        $builders = [
            (new DriverTrailerAssignmentsResource($this->samsara))->query()->whereDriver('driver-1'),
            (new DriverVehicleAssignmentsResource($this->samsara))->query()->where('filterBy', 'drivers'),
            $tachograph->driverActivityHistory()->between('2026-08-01T00:00:00Z', '2026-08-02T00:00:00Z'),
            $tachograph->driverFilesHistory()->between('2026-08-01T00:00:00Z', '2026-08-02T00:00:00Z'),
            $tachograph->vehicleFilesHistory()->between('2026-08-01T00:00:00Z', '2026-08-02T00:00:00Z'),
            $fuel->driverEfficiency()->between('2026-08-01T00:00:00Z', '2026-08-02T00:00:00Z'),
            $fuel->vehicleEfficiency()->between('2026-08-01T00:00:00Z', '2026-08-02T00:00:00Z'),
            (new IssuesResource($this->samsara))->query()->where('ids', 'issue-1'),
            (new IssuesResource($this->samsara))->stream()->where('startTime', '2026-08-01T00:00:00Z'),
            (new SpeedingResource($this->samsara))->intervalsStream()
                ->where('assetIds', 'asset-1')
                ->where('startTime', '2026-08-01T00:00:00Z'),
        ];

        foreach ($builders as $builder) {
            $builder->getWithPagination();
        }

        $this->http->assertNotSent(fn (Request $request): bool => array_key_exists('limit', $request->data()));
        $this->http->assertSentCount(count($builders));
    }

    #[Test]
    public function carrier_proposed_assignments_expose_the_official_cursor_page(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data'       => [['id' => 'assignment-1']],
                'pagination' => ['endCursor' => 'cursor-1', 'hasNextPage' => true],
            ]),
        ]);

        $page = (new CarrierProposedAssignmentsResource($this->samsara))
            ->query()
            ->whereDriver(['driver-1', 'driver-2'])
            ->getWithPagination();

        $this->assertInstanceOf(CursorPaginator::class, $page);
        $this->assertSame('cursor-1', $page->cursor()->getEndCursor());
        $this->assertTrue($page->hasMorePages());
        $this->assertRequest('GET', '/fleet/carrier-proposed-assignments', [
            'driverIds' => 'driver-1,driver-2',
            'limit'     => 100,
        ]);
    }

    #[Test]
    public function carrier_proposed_assignments_keep_only_the_three_exact_generic_shapes(): void
    {
        $resource = new CarrierProposedAssignmentsResource($this->samsara);
        $this->http->fake([
            '*' => $this->http->response([
                'data'       => [['id' => 'assignment-1']],
                'pagination' => ['hasNextPage' => false],
            ]),
        ]);
        $all = $resource->all();
        $this->assertSame(Entity::class, $all->first()::class);
        $this->assertRequest('GET', '/fleet/carrier-proposed-assignments');

        $payload = ['driverId' => 'driver-1', 'vehicleId' => 'vehicle-1'];
        $this->resetHttp();
        $this->http->fake(['*' => $this->http->response(['data' => ['id' => 'assignment-2']])]);
        $created = $resource->create($payload);
        $this->assertSame(Entity::class, $created::class);
        $this->assertRequest('POST', '/fleet/carrier-proposed-assignments', $payload);

        $this->resetHttp();
        $this->http->fake(['*' => $this->http->response([], 204)]);
        $this->assertTrue($resource->delete('assignment-2'));
        $this->assertRequest('DELETE', '/fleet/carrier-proposed-assignments/assignment-2');
    }

    #[Test]
    public function driver_trailer_assignments_require_a_filtered_builder_but_keep_generic_create(): void
    {
        $resource = new DriverTrailerAssignmentsResource($this->samsara);
        $this->http->fake([
            '*' => $this->http->response([
                'data'       => [['id' => 'assignment-1']],
                'pagination' => ['hasNextPage' => false],
            ]),
        ]);
        $assignments = $resource->query()->whereDriver(['driver-1', 'driver-2'])->get();
        $this->assertSame(Entity::class, $assignments->first()::class);
        $this->assertRequest('GET', '/driver-trailer-assignments', [
            'driverIds' => 'driver-1,driver-2',
        ]);

        $payload = ['driverId' => 'driver-1', 'trailerId' => 'trailer-1'];
        $this->resetHttp();
        $this->http->fake(['*' => $this->http->response(['data' => ['id' => 'assignment-2']])]);
        $created = $resource->create($payload);
        $this->assertSame(Entity::class, $created::class);
        $this->assertRequest('POST', '/driver-trailer-assignments', $payload);
    }

    #[Test]
    public function driver_vehicle_assignments_require_filter_by_but_keep_generic_create(): void
    {
        $resource = new DriverVehicleAssignmentsResource($this->samsara);
        $this->http->fake([
            '*' => $this->http->response([
                'data'       => [['id' => 'assignment-1']],
                'pagination' => ['hasNextPage' => false],
            ]),
        ]);
        $assignments = $resource->query()
            ->where('filterBy', 'drivers')
            ->whereDriver(['driver-1', 'driver-2'])
            ->get();
        $this->assertSame(Entity::class, $assignments->first()::class);
        $this->assertRequest('GET', '/fleet/driver-vehicle-assignments', [
            'filterBy'  => 'drivers',
            'driverIds' => 'driver-1,driver-2',
        ]);

        $payload = ['driverId' => 'driver-1', 'vehicleId' => 'vehicle-1'];
        $this->resetHttp();
        $this->http->fake(['*' => $this->http->response(['data' => ['id' => 'assignment-2']], 201)]);
        $created = $resource->create($payload);
        $this->assertSame(Entity::class, $created::class);
        $this->assertRequest('POST', '/fleet/driver-vehicle-assignments', $payload);
    }

    #[Test]
    public function fuel_and_energy_methods_use_the_current_paths_and_report_shapes(): void
    {
        $resource = new FuelAndEnergyResource($this->samsara);
        $purchase = [
            'fuelQuantityLiters'   => '100.5',
            'transactionLocation'  => 'Nashville, TN',
            'transactionPrice'     => ['amount' => '312.45', 'currency' => 'usd'],
            'transactionReference' => 'receipt-1',
            'transactionTime'      => '2026-08-01T00:00:00Z',
        ];
        $this->http->fake(['*' => $this->http->response(['data' => ['id' => 'purchase-1']])]);
        $created = $resource->createFuelPurchase($purchase);
        $this->assertIsArray($created);
        $this->assertRequest('POST', '/fuel-purchase', $purchase);

        $this->resetHttp();
        $this->http->fake([
            '*' => $this->http->response([
                'data'       => [['driverId' => 'driver-1']],
                'pagination' => ['hasNextPage' => false],
            ]),
        ]);
        $driverEfficiency = $resource->driverEfficiency()
            ->between('2026-08-01T00:00:00Z', '2026-08-01T01:00:00Z')
            ->whereDriver('driver-1')
            ->get();
        $this->assertSame(Entity::class, $driverEfficiency->first()::class);
        $this->assertRequest('GET', '/driver-efficiency/drivers', [
            'driverIds' => 'driver-1',
            'startTime' => '2026-08-01T00:00:00Z',
            'endTime'   => '2026-08-01T01:00:00Z',
        ]);

        $this->resetHttp();
        $this->http->fake([
            '*' => $this->http->response([
                'data'       => [['vehicleId' => 'vehicle-1']],
                'pagination' => ['hasNextPage' => false],
            ]),
        ]);
        $vehicleEfficiency = $resource->vehicleEfficiency()
            ->between('2026-08-01T00:00:00Z', '2026-08-01T01:00:00Z')
            ->where('vehicleIds', 'vehicle-1')
            ->get();
        $this->assertSame(Entity::class, $vehicleEfficiency->first()::class);
        $this->assertRequest('GET', '/driver-efficiency/vehicles', [
            'vehicleIds' => 'vehicle-1',
            'startTime'  => '2026-08-01T00:00:00Z',
            'endTime'    => '2026-08-01T01:00:00Z',
        ]);

        $dates = [
            'startDate' => '2026-08-01',
            'endDate'   => '2026-08-02',
            'driverIds' => ['driver-1', 'driver-2'],
        ];
        $driverReportEnvelope = [
            'data'       => ['driverReports' => [['driverId' => 'driver-1']]],
            'pagination' => ['hasNextPage' => false],
        ];
        $this->resetHttp();
        $this->http->fake(['*' => $this->http->response($driverReportEnvelope)]);
        $this->assertSame($driverReportEnvelope, $resource->driversFuelEnergyReport($dates));
        $this->assertRequest('GET', '/fleet/reports/drivers/fuel-energy', [
            'startDate' => '2026-08-01',
            'endDate'   => '2026-08-02',
            'driverIds' => 'driver-1,driver-2',
        ]);

        $vehicleReportEnvelope = [
            'data'       => ['vehicleReports' => [['vehicleId' => 'vehicle-1']]],
            'pagination' => ['hasNextPage' => false],
        ];
        $this->resetHttp();
        $this->http->fake(['*' => $this->http->response($vehicleReportEnvelope)]);
        $vehicleDates = ['startDate' => '2026-08-01', 'endDate' => '2026-08-02'];
        $this->assertSame($vehicleReportEnvelope, $resource->vehiclesFuelEnergyReport($vehicleDates));
        $this->assertRequest('GET', '/fleet/reports/vehicles/fuel-energy', $vehicleDates);
    }

    #[Test]
    public function idling_issues_and_speeding_use_filtered_current_builders(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data'       => [['id' => 'idling-1']],
                'pagination' => ['hasNextPage' => false],
            ]),
        ]);
        $idling = (new IdlingResource($this->samsara))->events()
            ->between('2026-08-01T00:00:00Z', '2026-08-01T01:00:00Z')
            ->get();
        $this->assertSame(Entity::class, $idling->first()::class);
        $this->assertRequest('GET', '/idling/events', [
            'startTime' => '2026-08-01T00:00:00Z',
            'endTime'   => '2026-08-01T01:00:00Z',
        ]);

        $this->resetHttp();
        $this->http->fake(['*' => $this->http->response(['data' => [['id' => 'issue-1']]])]);
        $issuesResource = new IssuesResource($this->samsara);
        $this->assertTrue(method_exists($issuesResource, 'query'));
        $issues = $issuesResource->query()
            ->where('ids', 'issue-1,issue-2')
            ->get();
        $this->assertSame(Entity::class, $issues->first()::class);
        $this->assertRequest('GET', '/issues', ['ids' => 'issue-1,issue-2']);

        $this->resetHttp();
        $this->http->fake([
            '*' => $this->http->response([
                'data'       => [['id' => 'issue-2']],
                'pagination' => ['hasNextPage' => false],
            ]),
        ]);
        $issueStream = (new IssuesResource($this->samsara))->stream()
            ->where('startTime', '2026-08-01T00:00:00Z')
            ->get();
        $this->assertSame(Entity::class, $issueStream->first()::class);
        $this->assertRequest('GET', '/issues/stream', [
            'startTime' => '2026-08-01T00:00:00Z',
        ]);

        $this->resetHttp();
        $this->http->fake([
            '*' => $this->http->response([
                'data'       => [['id' => 'speeding-1']],
                'pagination' => ['hasNextPage' => false],
            ]),
        ]);
        $speeding = (new SpeedingResource($this->samsara))->intervalsStream()
            ->where('assetIds', 'asset-1')
            ->where('startTime', '2026-08-01T00:00:00Z')
            ->get();
        $this->assertSame(Entity::class, $speeding->first()::class);
        $this->assertRequest('GET', '/speeding-intervals/stream', [
            'assetIds'  => 'asset-1',
            'startTime' => '2026-08-01T00:00:00Z',
        ]);
    }

    #[Test]
    public function ifta_methods_use_detail_jobs_and_current_report_paths(): void
    {
        $resource = new IftaResource($this->samsara);
        $detail = [
            'startHour' => '2026-08-01T00:00:00Z',
            'endHour'   => '2026-08-02T00:00:00Z',
        ];
        $job = [
            'args'            => $detail,
            'jobId'           => 'job-1',
            'jobStatus'       => 'Requested',
            'requestedAtTime' => '2026-08-01T00:00:00Z',
        ];
        $this->http->fake(['*' => $this->http->response(['data' => $job], 201)]);
        $this->assertSame($job, $resource->detailCsv($detail));
        $this->assertRequest('POST', '/ifta-detail/csv', $detail);

        $this->resetHttp();
        $completedJob = [
            ...$job,
            'jobStatus' => 'Completed',
            'files'     => [[
                'createdAtTime'             => '2026-08-01T01:00:00Z',
                'downloadUrl'               => 'https://example.test/ifta.csv.gz',
                'downloadUrlExpirationTime' => '2026-08-02T01:00:00Z',
                'name'                      => 'ifta.csv.gz',
                'recordCount'               => 10,
            ]],
        ];
        $this->http->fake(['*' => $this->http->response(['data' => $completedJob])]);
        $this->assertSame($completedJob, $resource->getDetailCsv('job-1'));
        $this->assertRequest('GET', '/ifta-detail/csv/job-1');

        $jurisdictionEnvelope = ['data' => ['jurisdictionReports' => [], 'year' => 2026]];
        $this->resetHttp();
        $this->http->fake(['*' => $this->http->response($jurisdictionEnvelope)]);
        $this->assertSame($jurisdictionEnvelope, $resource->jurisdictionReport(['year' => 2026]));
        $this->assertRequest('GET', '/fleet/reports/ifta/jurisdiction', ['year' => 2026]);

        $vehicleEnvelope = [
            'data'       => ['vehicleReports' => [], 'year' => 2026],
            'pagination' => ['hasNextPage' => false],
        ];
        $this->resetHttp();
        $this->http->fake(['*' => $this->http->response($vehicleEnvelope)]);
        $this->assertSame($vehicleEnvelope, $resource->vehicleReport(['year' => 2026]));
        $this->assertRequest('GET', '/fleet/reports/ifta/vehicle', ['year' => 2026]);
    }

    #[Test]
    public function issues_keep_the_current_generic_create_shape(): void
    {
        $payload = [
            'asset' => ['id' => 'asset-1'],
            'title' => 'Front bumper scratch',
        ];
        $this->http->fake(['*' => $this->http->response(['data' => ['id' => 'issue-1']], 201)]);

        $created = (new IssuesResource($this->samsara))->create($payload);

        $this->assertSame(Entity::class, $created::class);
        $this->assertRequest('POST', '/issues', $payload);
    }

    #[Test]
    public function tachograph_builders_use_the_current_fleet_driver_and_vehicle_paths(): void
    {
        $resource = new TachographResource($this->samsara);
        $cases = [
            [$resource->driverActivityHistory(), '/fleet/drivers/tachograph-activity/history'],
            [$resource->driverFilesHistory(), '/fleet/drivers/tachograph-files/history'],
            [$resource->vehicleFilesHistory(), '/fleet/vehicles/tachograph-files/history'],
        ];

        foreach ($cases as [$builder, $path]) {
            $this->resetHttp();
            $this->http->fake([
                '*' => $this->http->response([
                    'data'       => [['id' => 'tachograph-1']],
                    'pagination' => ['hasNextPage' => false],
                ]),
            ]);
            $result = $builder
                ->between('2026-08-01T00:00:00Z', '2026-08-01T01:00:00Z')
                ->get();
            $this->assertSame(Entity::class, $result->first()::class);
            $this->assertRequest('GET', $path, [
                'startTime' => '2026-08-01T00:00:00Z',
                'endTime'   => '2026-08-01T01:00:00Z',
            ]);
        }
    }

    #[Test]
    public function trailer_assignment_lookup_returns_the_unwrapped_trailer_shape(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'id'                 => 123,
                'name'               => 'Trailer 123',
                'trailerAssignments' => [['driverId' => 456]],
            ]),
        ]);

        $result = (new TrailerAssignmentsResource($this->samsara))->forTrailer(123, [
            'startMs' => 1_754_006_400_000,
            'endMs'   => 1_754_092_800_000,
        ]);

        $this->assertInstanceOf(Trailer::class, $result);
        $this->assertRequest('GET', '/v1/fleet/trailers/123/assignments', [
            'startMs' => 1_754_006_400_000,
            'endMs'   => 1_754_092_800_000,
        ]);
    }

    /**
     * Assert the exact HTTP method, path, and body/query data.
     *
     * @param  array<string, mixed>  $data
     */
    private function assertRequest(string $method, string $path, array $data = []): void
    {
        $this->http->assertSent(fn (Request $request): bool => $request->method() === $method
            && parse_url($request->url(), PHP_URL_PATH) === $path
            && $request->data() == $data);
    }

    /**
     * Reset the fake factory so each grouped assertion receives its own fixture.
     */
    private function resetHttp(): void
    {
        $this->http = new HttpFactory;
        $this->samsara->setHttpFactory($this->http);
    }
}
