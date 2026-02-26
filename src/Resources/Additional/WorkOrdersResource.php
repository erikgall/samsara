<?php

namespace Samsara\Resources\Additional;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;
use Samsara\Data\EntityCollection;
use Samsara\Data\Maintenance\WorkOrder;

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
        return new Builder($this);
    }

    /**
     * Get all service tasks.
     *
     * @return EntityCollection<int, \Samsara\Data\Entity>
     */
    public function serviceTasks(): EntityCollection
    {
        $response = $this->client()->get('/maintenance/service-tasks');

        $this->handleError($response);

        return $this->mapToEntities($response->json('data', []));
    }

    /**
     * Get a query builder for work orders stream.
     */
    public function stream(): Builder
    {
        return $this->createBuilderWithEndpoint('/maintenance/work-orders/stream');
    }

    /**
     * Upload an invoice scan.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function uploadInvoiceScan(array $data): array
    {
        $response = $this->client()->post('/maintenance/invoice-scans', $data);

        $this->handleError($response);

        return $response->json('data', $response->json());
    }
}
