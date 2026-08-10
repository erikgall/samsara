<?php

namespace Samsara\Tests\Unit\Resources\Additional;

use Samsara\Samsara;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use Samsara\Data\EntityCollection;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\Maintenance\WorkOrder;
use Samsara\Data\Maintenance\ServiceTask;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Resources\Additional\WorkOrdersResource;
use Samsara\Exceptions\UnsupportedOperationException;

/**
 * Unit tests for the WorkOrdersResource class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class WorkOrdersResourceTest extends TestCase
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
    public function it_can_create_work_order(): void
    {
        $this->http->fake([
            '*/maintenance/work-orders' => $this->http->response([
                'data' => [
                    'id' => 'wo-123',
                ],
            ], 200),
        ]);

        $resource = new WorkOrdersResource($this->samsara);

        $result = $resource->create(['assetId' => 'asset-1']);

        $this->assertInstanceOf(WorkOrder::class, $result);
    }

    #[Test]
    public function it_can_delete_work_order(): void
    {
        $resource = new WorkOrdersResource($this->samsara);

        $this->expectException(UnsupportedOperationException::class);

        $resource->delete('wo-123');
    }

    #[Test]
    public function it_can_find_work_order(): void
    {
        $resource = new WorkOrdersResource($this->samsara);

        $this->expectException(UnsupportedOperationException::class);

        $resource->find('wo-123');
    }

    #[Test]
    public function it_can_get_all_work_orders(): void
    {
        $this->http->fake([
            '*/maintenance/work-orders' => $this->http->response([
                'data' => [
                    ['id' => 'wo-1'],
                    ['id' => 'wo-2'],
                ],
            ], 200),
        ]);

        $resource = new WorkOrdersResource($this->samsara);

        $result = $resource->all();

        $this->assertInstanceOf(EntityCollection::class, $result);
        $this->assertCount(2, $result);
    }

    #[Test]
    public function it_can_get_service_tasks(): void
    {
        $this->http->fake([
            '*/maintenance/service-tasks' => $this->http->response([
                'data' => [
                    ['id' => 'task-1'],
                    ['id' => 'task-2'],
                ],
            ], 200),
        ]);

        $resource = new WorkOrdersResource($this->samsara);

        $result = $resource->serviceTasks()->get();

        $this->assertInstanceOf(ServiceTask::class, $result->first());
        $this->assertCount(2, $result);
    }

    #[Test]
    public function it_can_get_stream_builder(): void
    {
        $resource = new WorkOrdersResource($this->samsara);

        $builder = $resource->stream();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_update_work_order(): void
    {
        $resource = new WorkOrdersResource($this->samsara);

        $this->expectException(UnsupportedOperationException::class);

        $resource->update('wo-123', ['status' => 'completed']);
    }

    #[Test]
    public function it_can_upload_invoice_scan(): void
    {
        $this->http->fake([
            '*/maintenance/invoice-scans' => $this->http->response([
                'data' => [
                    'workOrderId' => 'wo-123',
                ],
            ], 200),
        ]);

        $resource = new WorkOrdersResource($this->samsara);

        $result = $resource->uploadInvoiceScan([
            'file' => [
                'base64Content' => 'base64data',
                'contentType'   => 'application/pdf',
            ],
            'workOrderId' => 'wo-123',
        ]);

        $this->assertIsArray($result);
        $this->assertSame('wo-123', $result['workOrderId']);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new WorkOrdersResource($this->samsara);

        $this->assertSame('/maintenance/work-orders', $resource->getEndpoint());
    }
}
