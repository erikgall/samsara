<?php

namespace ErikGall\Samsara\Tests\Unit\Resources\Additional;

use ErikGall\Samsara\Samsara;
use ErikGall\Samsara\Query\Builder;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Data\EntityCollection;
use ErikGall\Samsara\Data\Maintenance\WorkOrder;
use Illuminate\Http\Client\Factory as HttpFactory;
use ErikGall\Samsara\Resources\Additional\WorkOrdersResource;

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
            ], 201),
        ]);

        $resource = new WorkOrdersResource($this->samsara);

        $result = $resource->create(['vehicleId' => 'vehicle-1']);

        $this->assertInstanceOf(WorkOrder::class, $result);
    }

    #[Test]
    public function it_can_delete_work_order(): void
    {
        $this->http->fake([
            '*/maintenance/work-orders/wo-123' => $this->http->response([], 204),
        ]);

        $resource = new WorkOrdersResource($this->samsara);

        $result = $resource->delete('wo-123');

        $this->assertTrue($result);
    }

    #[Test]
    public function it_can_find_work_order(): void
    {
        $this->http->fake([
            '*/maintenance/work-orders/wo-123' => $this->http->response([
                'data' => [
                    'id'     => 'wo-123',
                    'status' => 'open',
                ],
            ], 200),
        ]);

        $resource = new WorkOrdersResource($this->samsara);

        $result = $resource->find('wo-123');

        $this->assertInstanceOf(WorkOrder::class, $result);
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

        $result = $resource->serviceTasks();

        $this->assertInstanceOf(EntityCollection::class, $result);
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
        $this->http->fake([
            '*/maintenance/work-orders/wo-123' => $this->http->response([
                'data' => [
                    'id'     => 'wo-123',
                    'status' => 'completed',
                ],
            ], 200),
        ]);

        $resource = new WorkOrdersResource($this->samsara);

        $result = $resource->update('wo-123', ['status' => 'completed']);

        $this->assertInstanceOf(WorkOrder::class, $result);
    }

    #[Test]
    public function it_can_upload_invoice_scan(): void
    {
        $this->http->fake([
            '*/maintenance/invoice-scans' => $this->http->response([
                'data' => [
                    'id' => 'scan-123',
                ],
            ], 201),
        ]);

        $resource = new WorkOrdersResource($this->samsara);

        $result = $resource->uploadInvoiceScan(['file' => 'base64data']);

        $this->assertIsArray($result);
        $this->assertSame('scan-123', $result['id']);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new WorkOrdersResource($this->samsara);

        $this->assertSame('/maintenance/work-orders', $resource->getEndpoint());
    }
}
