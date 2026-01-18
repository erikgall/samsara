<?php

namespace Samsara\Tests\Unit\Resources\Additional;

use Samsara\Samsara;
use Samsara\Tests\TestCase;
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
            '*ifta/csv-exports/export-123' => $this->http->response([
                'data' => [
                    'id'     => 'export-123',
                    'status' => 'completed',
                    'url'    => 'https://example.com/csv',
                ],
            ], 200),
        ]);

        $resource = new IftaResource($this->samsara);

        $result = $resource->getDetailCsv('export-123');

        $this->assertIsArray($result);
        $this->assertSame('completed', $result['status']);
    }

    #[Test]
    public function it_can_get_jurisdiction_report(): void
    {
        $this->http->fake([
            '*ifta/jurisdiction-report*' => $this->http->response([
                'data' => [
                    ['jurisdiction' => 'CA', 'miles' => 1000],
                ],
            ], 200),
        ]);

        $resource = new IftaResource($this->samsara);

        $result = $resource->jurisdictionReport(['year' => 2024, 'quarter' => 1]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
    }

    #[Test]
    public function it_can_get_vehicle_report(): void
    {
        $this->http->fake([
            '*ifta/vehicle-report*' => $this->http->response([
                'data' => [
                    ['vehicleId' => 'vehicle-1', 'totalMiles' => 5000],
                ],
            ], 200),
        ]);

        $resource = new IftaResource($this->samsara);

        $result = $resource->vehicleReport(['year' => 2024, 'quarter' => 1]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
    }

    #[Test]
    public function it_can_request_detail_csv(): void
    {
        $this->http->fake([
            '*ifta/csv-exports' => $this->http->response([
                'data' => [
                    'id'     => 'export-123',
                    'status' => 'pending',
                ],
            ], 201),
        ]);

        $resource = new IftaResource($this->samsara);

        $result = $resource->detailCsv(['year' => 2024, 'quarter' => 1]);

        $this->assertIsArray($result);
        $this->assertSame('export-123', $result['id']);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new IftaResource($this->samsara);

        $this->assertSame('/ifta', $resource->getEndpoint());
    }
}
