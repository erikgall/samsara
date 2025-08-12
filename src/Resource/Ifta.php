<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\Ifta\GetIftaDetailJob;
use ErikGall\Samsara\Requests\Ifta\CreateIftaDetailJob;
use ErikGall\Samsara\Requests\Ifta\GetIftaVehicleReports;
use ErikGall\Samsara\Requests\Ifta\GetIftaJurisdictionReports;

class Ifta extends Resource
{
    /**
     * Create an IFTA detail job.
     *
     * @param  array  $payload
     * @return Response
     */
    public function create(array $payload = []): Response
    {
        return $this->connector->send(new CreateIftaDetailJob($payload));
    }

    /**
     * Find an IFTA detail job by ID.
     *
     * @param  string  $id  ID of the requested job.
     * @return Response
     */
    public function find(string $id): Response
    {
        return $this->connector->send(new GetIftaDetailJob($id));
    }

    /**
     * Get IFTA jurisdiction reports.
     *
     * @param  int  $year  The year of the requested IFTA report summary.
     * @param  string|null  $month  The month of the requested IFTA report summary.
     * @param  string|null  $quarter  The quarter of the requested IFTA report summary.
     * @param  string|null  $jurisdictions  Filter by jurisdictions.
     * @param  string|null  $fuelType  Filter by IFTA fuel types.
     * @param  string|null  $vehicleIds  Filter by vehicle IDs and externalIds.
     * @param  string|null  $tagIds  Filter by tag IDs.
     * @param  string|null  $parentTagIds  Filter by parent tag IDs.
     * @return Response
     */
    public function getJurisdictionReports(
        int $year,
        ?string $month = null,
        ?string $quarter = null,
        ?string $jurisdictions = null,
        ?string $fuelType = null,
        ?string $vehicleIds = null,
        ?string $tagIds = null,
        ?string $parentTagIds = null
    ): Response {
        return $this->connector->send(
            new GetIftaJurisdictionReports(
                $year,
                $month,
                $quarter,
                $jurisdictions,
                $fuelType,
                $vehicleIds,
                $tagIds,
                $parentTagIds
            )
        );
    }

    /**
     * Get IFTA vehicle reports.
     *
     * @param  int  $year  The year of the requested IFTA report summary.
     * @param  string|null  $month  The month of the requested IFTA report summary.
     * @param  string|null  $quarter  The quarter of the requested IFTA report summary.
     * @param  string|null  $jurisdictions  Filter by jurisdictions.
     * @param  string|null  $fuelType  Filter by IFTA fuel types.
     * @param  string|null  $vehicleIds  Filter by vehicle IDs and externalIds.
     * @param  string|null  $tagIds  Filter by tag IDs.
     * @param  string|null  $parentTagIds  Filter by parent tag IDs.
     * @return Response
     */
    public function getVehicleReports(
        int $year,
        ?string $month = null,
        ?string $quarter = null,
        ?string $jurisdictions = null,
        ?string $fuelType = null,
        ?string $vehicleIds = null,
        ?string $tagIds = null,
        ?string $parentTagIds = null
    ): Response {
        return $this->connector->send(
            new GetIftaVehicleReports(
                $year,
                $month,
                $quarter,
                $jurisdictions,
                $fuelType,
                $vehicleIds,
                $tagIds,
                $parentTagIds
            )
        );
    }
}
