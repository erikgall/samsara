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
     * Delete a work order using its required query ID.
     */
    public function deleteWorkOrder(string $id): bool
    {
        $requestEndpoint = $this->endpoint.'?id='.rawurlencode($id);
        $response = $this->executeSdkRequest(
            fn () => $this->client()->delete($requestEndpoint),
            $this->endpoint,
        );

        return $response->successful();
    }

    /**
     * Create a new query builder for this resource.
     */
    public function query(): Builder
    {
        return $this->createBuilderWithEndpoint($this->endpoint)
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
     * Update a work order using the contract's collection-level PATCH operation.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateWorkOrder(array $data): WorkOrder
    {
        $response = $this->executeSdkRequest(
            fn () => $this->client()->patch($this->endpoint, $data),
            $this->endpoint,
        );

        return WorkOrder::make($this->decodeJsonData($response, $this->endpoint, false));
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

        return $this->decodeJsonData($response, $endpoint);
    }

    /**
     * {@inheritDoc}
     */
    protected function supportedGenericOperations(): array
    {
        return ['all', 'create'];
    }
}
