<?php

namespace Samsara\Tests\Unit\Resources\Safety;

use Samsara\Samsara;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use Samsara\Data\EntityCollection;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\HoursOfService\HosLog;
use Samsara\Data\HoursOfService\HosLogsForDriver;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Resources\Safety\HoursOfServiceResource;

/**
 * Unit tests for the HoursOfServiceResource.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class HoursOfServiceResourceTest extends TestCase
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
        $resource = new HoursOfServiceResource($this->samsara);

        $this->assertInstanceOf(HoursOfServiceResource::class, $resource);
    }

    #[Test]
    public function it_can_get_all_logs(): void
    {
        $this->http->fake([
            '*' => $this->http->response([
                'data' => [
                    [
                        'driver' => [
                            'id'   => 'driver-1',
                            'name' => 'John Doe',
                        ],
                        'hosLogs' => [
                            [
                                'id'     => 'log-1',
                                'status' => 'onDuty',
                            ],
                        ],
                    ],
                    [
                        'driver' => [
                            'id'   => 'driver-2',
                            'name' => 'Jane Doe',
                        ],
                        'hosLogs' => [
                            [
                                'id'     => 'log-2',
                                'status' => 'offDuty',
                            ],
                        ],
                    ],
                ],
            ]),
        ]);

        $resource = new HoursOfServiceResource($this->samsara);
        $logs = $resource->all();

        $this->assertInstanceOf(EntityCollection::class, $logs);
        $this->assertCount(2, $logs);
        $this->assertInstanceOf(HosLogsForDriver::class, $logs->first());
        $this->assertInstanceOf(HosLog::class, $logs->first()->getLogs()->first());
    }

    #[Test]
    public function it_can_return_clocks_query_builder(): void
    {
        $resource = new HoursOfServiceResource($this->samsara);
        $query = $resource->clocks();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_return_daily_logs_query_builder(): void
    {
        $resource = new HoursOfServiceResource($this->samsara);
        $query = $resource->dailyLogs();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_return_logs_query_builder(): void
    {
        $resource = new HoursOfServiceResource($this->samsara);
        $query = $resource->logs();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_return_query_builder(): void
    {
        $resource = new HoursOfServiceResource($this->samsara);
        $query = $resource->query();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_can_return_violations_query_builder(): void
    {
        $resource = new HoursOfServiceResource($this->samsara);
        $query = $resource->violations();

        $this->assertInstanceOf(Builder::class, $query);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new HoursOfServiceResource($this->samsara);

        $this->assertSame('/fleet/hos/logs', $resource->getEndpoint());
    }

    #[Test]
    public function it_has_correct_entity_class(): void
    {
        $resource = new HoursOfServiceResource($this->samsara);

        $this->assertSame(HosLogsForDriver::class, $resource->getEntityClass());
    }
}
