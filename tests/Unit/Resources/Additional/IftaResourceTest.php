<?php

namespace Samsara\Tests\Unit\Resources\Additional;

use Samsara\Samsara;
use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use Illuminate\Http\Client\Request;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Resources\Additional\IftaResource;
use Illuminate\Http\Client\Factory as HttpFactory;

/**
 * Unit tests for the IftaResource class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class IftaResourceTest extends TestCase
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
    public function it_can_get_detail_csv(): void
    {
        $this->http->fake([
            '*ifta-detail/csv/export-123' => $this->http->response([
                'data' => [
                    'args' => [
                        'startHour' => '2024-01-01T00:00:00Z',
                        'endHour'   => '2024-02-01T00:00:00Z',
                    ],
                    'jobId'           => 'export-123',
                    'jobStatus'       => 'Completed',
                    'requestedAtTime' => '2024-01-01T00:00:00Z',
                    'files'           => [[
                        'createdAtTime'             => '2024-01-01T01:00:00Z',
                        'downloadUrl'               => 'https://example.com/csv',
                        'downloadUrlExpirationTime' => '2024-01-02T01:00:00Z',
                        'name'                      => 'ifta.csv.gz',
                        'recordCount'               => 10,
                    ]],
                ],
            ], 200),
        ]);

        $resource = new IftaResource($this->samsara);

        $result = $resource->getDetailCsv('export-123');

        $this->assertIsArray($result);
        $this->assertSame('Completed', $result['jobStatus']);
        $this->assertSame('https://example.com/csv', $result['files'][0]['downloadUrl']);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/ifta-detail/csv/export-123');
    }

    #[Test]
    public function it_can_get_jurisdiction_report(): void
    {
        $this->http->fake([
            '*fleet/reports/ifta/jurisdiction*' => $this->http->response([
                'data' => [
                    ['jurisdiction' => 'CA', 'miles' => 1000],
                ],
            ], 200),
        ]);

        $resource = new IftaResource($this->samsara);

        $result = $resource->jurisdictionReport(['year' => 2024, 'quarter' => 'Q1']);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/reports/ifta/jurisdiction'
            && $request->data() === ['year' => 2024, 'quarter' => 'Q1']);
    }

    #[Test]
    public function it_can_get_vehicle_report(): void
    {
        $this->http->fake([
            '*fleet/reports/ifta/vehicle*' => $this->http->response([
                'data' => [
                    ['vehicleId' => 'vehicle-1', 'totalMiles' => 5000],
                ],
            ], 200),
        ]);

        $resource = new IftaResource($this->samsara);

        $result = $resource->vehicleReport(['year' => 2024, 'quarter' => 'Q1']);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/fleet/reports/ifta/vehicle'
            && $request->data() === ['year' => 2024, 'quarter' => 'Q1']);
    }

    #[Test]
    public function it_can_request_detail_csv(): void
    {
        $this->http->fake([
            '*ifta-detail/csv' => $this->http->response([
                'data' => [
                    'args' => [
                        'startHour' => '2024-01-01T00:00:00Z',
                        'endHour'   => '2024-02-01T00:00:00Z',
                    ],
                    'jobId'           => 'export-123',
                    'jobStatus'       => 'Requested',
                    'requestedAtTime' => '2024-01-01T00:00:00Z',
                ],
            ], 201),
        ]);

        $resource = new IftaResource($this->samsara);

        $payload = [
            'startHour' => '2024-01-01T00:00:00Z',
            'endHour'   => '2024-02-01T00:00:00Z',
        ];
        $result = $resource->detailCsv($payload);

        $this->assertIsArray($result);
        $this->assertSame('export-123', $result['jobId']);
        $this->assertSame('Requested', $result['jobStatus']);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'POST'
            && parse_url($request->url(), PHP_URL_PATH) === '/ifta-detail/csv'
            && $request->data() === $payload);
    }

    #[Test]
    public function it_encodes_the_detail_job_id_as_one_path_segment(): void
    {
        $this->http->fake([
            '*' => $this->http->response(['data' => ['jobId' => 'folder/export-123']]),
        ]);

        $resource = new IftaResource($this->samsara);
        $result = $resource->getDetailCsv('folder/export-123');

        $this->assertSame('folder/export-123', $result['jobId']);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'GET'
            && parse_url($request->url(), PHP_URL_PATH) === '/ifta-detail/csv/folder%2Fexport-123');
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new IftaResource($this->samsara);

        $this->assertSame('/ifta-detail/csv', $resource->getEndpoint());
    }

    #[Test]
    public function it_keeps_the_raw_vehicle_report_envelope_authoritative(): void
    {
        $envelope = [
            'data' => [
                'year'            => 2024,
                'quarter'         => 'Q1',
                'vehicleReports'  => [['vehicleId' => 'vehicle-1', 'totalMiles' => 5000]],
                'troubleshooting' => ['message' => 'Retained only by the raw method'],
            ],
            'pagination' => ['endCursor' => 'vehicle-next', 'hasNextPage' => true],
        ];
        $this->http->fake(['*' => $this->http->response($envelope)]);

        $result = (new IftaResource($this->samsara))->vehicleReport(['year' => 2024]);

        $this->assertSame($envelope, $result);
    }

    #[Test]
    public function it_paginates_nested_ifta_vehicle_reports_without_an_unsupported_limit(): void
    {
        $this->http->fakeSequence()
            ->push([
                'data' => [
                    'year'           => 2024,
                    'vehicleReports' => [['id' => 'ifta-report-1', 'vehicleId' => 'vehicle-1']],
                ],
                'pagination' => ['endCursor' => 'ifta-next', 'hasNextPage' => true],
            ])
            ->push([
                'data' => [
                    'year'           => 2024,
                    'vehicleReports' => [['id' => 'ifta-report-2', 'vehicleId' => 'vehicle-2']],
                ],
                'pagination' => ['endCursor' => '', 'hasNextPage' => false],
            ]);

        $page = (new IftaResource($this->samsara))
            ->vehicleReportQuery()
            ->where('year', 2024)
            ->paginate();
        $nextPage = $page->nextPage();

        $this->assertInstanceOf(Entity::class, $page->items()->first());
        $this->assertSame('ifta-report-1', $page->items()->first()->getId());
        $this->assertSame('ifta-report-2', $nextPage?->items()->first()->getId());
        $this->http->assertSentCount(2);
        $this->http->assertSent(fn (Request $request): bool => $request->data() === [
            'year'  => 2024,
            'after' => 'ifta-next',
        ]);
    }
}
