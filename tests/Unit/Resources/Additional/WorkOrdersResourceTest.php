<?php

namespace Samsara\Tests\Unit\Resources\Additional;

use Samsara\Samsara;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use Samsara\Data\EntityCollection;
use Illuminate\Http\Client\Request;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\Maintenance\WorkOrder;
use Samsara\Data\Maintenance\ServiceTask;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Resources\Additional\WorkOrdersResource;

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
        $this->http->preventStrayRequests();
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
    public function it_deletes_a_work_order_with_the_contract_query_id(): void
    {
        $this->http->fake(['*' => $this->http->response(status: 204)]);

        $deleted = (new WorkOrdersResource($this->samsara))->deleteWorkOrder('wo/123');

        $this->assertTrue($deleted);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'DELETE'
            && parse_url($request->url(), PHP_URL_PATH) === '/maintenance/work-orders'
            && $this->queryFor($request->url()) === ['id' => 'wo/123']);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new WorkOrdersResource($this->samsara);

        $this->assertSame('/maintenance/work-orders', $resource->getEndpoint());
    }

    #[Test]
    public function it_queries_work_orders(): void
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

        $result = $resource->query()->get();

        $this->assertInstanceOf(EntityCollection::class, $result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(WorkOrder::class, $result->first());
    }

    #[Test]
    public function it_updates_a_work_order_at_the_collection_endpoint(): void
    {
        $this->http->fake([
            '*' => $this->http->response(['data' => [
                'id'     => 'wo-123',
                'status' => 'Completed',
            ]]),
        ]);

        $workOrder = (new WorkOrdersResource($this->samsara))->updateWorkOrder([
            'id'     => 'wo-123',
            'status' => 'Completed',
        ]);

        $this->assertInstanceOf(WorkOrder::class, $workOrder);
        $this->assertSame('wo-123', $workOrder->id);
        $this->http->assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
            && parse_url($request->url(), PHP_URL_PATH) === '/maintenance/work-orders'
            && $request->data() === ['id' => 'wo-123', 'status' => 'Completed']);
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
