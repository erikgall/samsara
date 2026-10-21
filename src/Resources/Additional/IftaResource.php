<?php

namespace Samsara\Resources\Additional;

use Samsara\Data\Entity;
use Samsara\Query\Builder;
use Samsara\Resources\Resource;

/**
 * IFTA resource for the Samsara API.
 *
 * Provides access to IFTA (International Fuel Tax Agreement) reporting endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @extends \Samsara\Resources\Resource<Entity>
 */
class IftaResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/ifta-detail/csv';

    /**
     * Request a detail CSV export.
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public function detailCsv(array $params): array
    {
        $endpoint = '/ifta-detail/csv';
        $response = $this->executeSdkRequest(
            fn () => $this->client()->post($endpoint, $params),
            $endpoint
        );

        return $this->decodeJsonData($response, $endpoint);
    }

    /**
     * Get a detail CSV export by ID.
     *
     * @return array<string, mixed>
     */
    public function getDetailCsv(string $id): array
    {
        $endpoint = '/ifta-detail/csv/'.rawurlencode($id);
        $response = $this->executeSdkRequest(
            fn () => $this->client()->get($endpoint),
            $endpoint
        );

        return $this->decodeJsonData($response, $endpoint);
    }

    /**
     * Get IFTA jurisdiction report.
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public function jurisdictionReport(array $params): array
    {
        $endpoint = '/fleet/reports/ifta/jurisdiction';
        $response = $this->executeSdkGet($endpoint, $params);

        return $this->decodeJsonArray($response, $endpoint);
    }

    /**
     * Get IFTA vehicle report.
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public function vehicleReport(array $params): array
    {
        $endpoint = '/fleet/reports/ifta/vehicle';
        $response = $this->executeSdkGet($endpoint, $params);

        return $this->decodeJsonArray($response, $endpoint);
    }

    /**
     * Get a query builder for IFTA vehicle reports.
     *
     * @return Builder<Entity>
     */
    public function vehicleReportQuery(): Builder
    {
        return $this->newQuery('/fleet/reports/ifta/vehicle')
            ->withoutDefaultPageSize()
            ->withResponseCollectionKey('data.vehicleReports');
    }
}
