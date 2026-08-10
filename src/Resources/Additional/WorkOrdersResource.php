<?php

namespace Samsara\Resources\Additional;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Data\Maintenance\WorkOrder;
use Samsara\Data\Maintenance\ServiceTask;

/**
 * WorkOrders resource for the Samsara API.
 *
 * Provides access to maintenance work order endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class WorkOrdersResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/maintenance/work-orders';

    /**
     * The entity class for this resource.
     *
     * @var class-string<WorkOrder>
     */
    protected string $entity = WorkOrder::class;

    /**
     * Create a new query builder for this resource.
     */
    public function query(): Builder
    {
        return (new Builder($this))
            ->withoutDefaultPageSize()
            ->repeatQueryParameters('ids');
    }

    /**
     * Get all service tasks.
     */
    public function serviceTasks(): Builder
    {
        return $this->createBuilderWithEndpoint('/maintenance/service-tasks')
            ->withoutDefaultPageSize()
            ->repeatQueryParameters('ids')
            ->usingEntity(ServiceTask::class);
    }

    /**
     * Get a query builder for work orders stream.
     */
    public function stream(): Builder
    {
        return $this->createBuilderWithEndpoint('/maintenance/work-orders/stream')
            ->withoutDefaultPageSize()
            ->repeatQueryParameters('workOrderStatuses', 'assetIds', 'assignedUserIds')
            ->usingEntity(WorkOrder::class);
    }

    /**
     * Upload an invoice scan.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function uploadInvoiceScan(array $data): array
    {
        $endpoint = '/maintenance/invoice-scans';
        $response = $this->executeSdkRequest(
            fn () => $this->client()->post($endpoint, $data),
            $endpoint
        );

        return $response->json('data', $response->json());
    }

    /**
     * {@inheritDoc}
     */
    protected function supportedGenericOperations(): array
    {
        return ['all', 'create'];
    }
}
