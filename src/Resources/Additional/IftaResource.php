<?php

namespace ErikGall\Samsara\Resources\Additional;

use ErikGall\Samsara\Resources\Resource;

/**
 * IFTA resource for the Samsara API.
 *
 * Provides access to IFTA (International Fuel Tax Agreement) reporting endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class IftaResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/ifta';

    /**
     * Request a detail CSV export.
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public function detailCsv(array $params): array
    {
        $response = $this->client()->post('/ifta/csv-exports', $params);

        $this->handleError($response);

        return $response->json('data', $response->json());
    }

    /**
     * Get a detail CSV export by ID.
     *
     * @return array<string, mixed>
     */
    public function getDetailCsv(string $id): array
    {
        $response = $this->client()->get("/ifta/csv-exports/{$id}");

        $this->handleError($response);

        return $response->json('data', $response->json());
    }

    /**
     * Get IFTA jurisdiction report.
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public function jurisdictionReport(array $params): array
    {
        $response = $this->client()->get('/ifta/jurisdiction-report', $params);

        $this->handleError($response);

        return $response->json();
    }

    /**
     * Get IFTA vehicle report.
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public function vehicleReport(array $params): array
    {
        $response = $this->client()->get('/ifta/vehicle-report', $params);

        $this->handleError($response);

        return $response->json();
    }
}
