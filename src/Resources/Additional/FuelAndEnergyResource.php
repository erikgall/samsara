<?php

namespace Samsara\Resources\Additional;

use Samsara\Data\Entity;
use Samsara\Query\Builder;
use Samsara\Resources\Resource;

/**
 * FuelAndEnergy resource for the Samsara API.
 *
 * Provides access to fuel and energy efficiency endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @extends \Samsara\Resources\Resource<Entity>
 */
class FuelAndEnergyResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/fuel-purchase';

    /**
     * Create a fuel purchase record.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function createFuelPurchase(array $data): array
    {
        $endpoint = '/fuel-purchase';
        $response = $this->executeSdkRequest(
            fn () => $this->client()->post($endpoint, $data),
            $endpoint
        );

        return $this->decodeJsonData($response, $endpoint);
    }

    /**
     * Get a query builder for driver efficiency.
     *
     * @return Builder<Entity>
     */
    public function driverEfficiency(): Builder
    {
        return $this->newQuery('/driver-efficiency/drivers')
            ->withoutDefaultPageSize();
    }

    /**
     * Get drivers fuel and energy report.
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public function driversFuelEnergyReport(array $params): array
    {
        if (is_array($params['driverIds'] ?? null) && $params['driverIds'] !== []) {
            $params['driverIds'] = implode(',', $params['driverIds']);
        }

        $endpoint = '/fleet/reports/drivers/fuel-energy';
        $response = $this->executeSdkGet($endpoint, $params);

        return $this->decodeJsonArray($response, $endpoint);
    }

    /**
     * Get a query builder for drivers fuel and energy reports.
     *
     * @return Builder<Entity>
     */
    public function driversFuelEnergyReportQuery(): Builder
    {
        return $this->newQuery('/fleet/reports/drivers/fuel-energy')
            ->withoutDefaultPageSize()
            ->withResponseCollectionKey('data.driverReports');
    }

    /**
     * Get a query builder for vehicle efficiency.
     *
     * @return Builder<Entity>
     */
    public function vehicleEfficiency(): Builder
    {
        return $this->newQuery('/driver-efficiency/vehicles')
            ->withoutDefaultPageSize();
    }

    /**
     * Get vehicles fuel and energy report.
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public function vehiclesFuelEnergyReport(array $params): array
    {
        $endpoint = '/fleet/reports/vehicles/fuel-energy';
        $response = $this->executeSdkGet($endpoint, $params);

        return $this->decodeJsonArray($response, $endpoint);
    }

    /**
     * Get a query builder for vehicles fuel and energy reports.
     *
     * @return Builder<Entity>
     */
    public function vehiclesFuelEnergyReportQuery(): Builder
    {
        return $this->newQuery('/fleet/reports/vehicles/fuel-energy')
            ->withoutDefaultPageSize()
            ->withResponseCollectionKey('data.vehicleReports');
    }
}
