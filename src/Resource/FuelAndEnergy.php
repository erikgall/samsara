<?php

namespace ErikGall\Samsara\Resource;

use Saloon\Http\Response;
use ErikGall\Samsara\Resource;
use ErikGall\Samsara\Requests\FuelAndEnergy\PostFuelPurchase;
use ErikGall\Samsara\Requests\FuelAndEnergy\GetFuelEnergyDriverReports;
use ErikGall\Samsara\Requests\FuelAndEnergy\GetFuelEnergyVehicleReports;

class FuelAndEnergy extends Resource
{
    /**
     * Create a new fuel purchase record.
     *
     * @param  array  $payload  The data to create the fuel purchase.
     * @return Response
     */
    public function create(array $payload = []): Response
    {
        return $this->connector->send(new PostFuelPurchase($payload));
    }

    /**
     * Get fuel and energy driver reports.
     *
     * @param  string  $startDate  Start date (RFC 3339).
     * @param  string  $endDate  End date (RFC 3339).
     * @param  array|null  $driverIds  Filter by driver IDs.
     * @param  string|null  $tagIds  Filter by tag IDs.
     * @param  string|null  $parentTagIds  Filter by parent tag IDs.
     * @return Response
     */
    public function getDriverReports(
        string $startDate,
        string $endDate,
        ?array $driverIds = null,
        ?string $tagIds = null,
        ?string $parentTagIds = null
    ): Response {
        return $this->connector->send(
            new GetFuelEnergyDriverReports($startDate, $endDate, $driverIds, $tagIds, $parentTagIds)
        );
    }

    /**
     * Get fuel and energy vehicle reports.
     *
     * @param  string  $startDate  Start date (RFC 3339).
     * @param  string  $endDate  End date (RFC 3339).
     * @param  string|null  $vehicleIds  Filter by vehicle IDs.
     * @param  string|null  $energyType  The type of energy used by the vehicle.
     * @param  string|null  $tagIds  Filter by tag IDs.
     * @param  string|null  $parentTagIds  Filter by parent tag IDs.
     * @return Response
     */
    public function getVehicleReports(
        string $startDate,
        string $endDate,
        ?string $vehicleIds = null,
        ?string $energyType = null,
        ?string $tagIds = null,
        ?string $parentTagIds = null
    ): Response {
        return $this->connector->send(
            new GetFuelEnergyVehicleReports(
                $startDate,
                $endDate,
                $vehicleIds,
                $energyType,
                $tagIds,
                $parentTagIds
            )
        );
    }
}
