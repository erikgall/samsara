<?php

namespace Samsara\Resources\Additional;

use Samsara\Query\Builder;
use Samsara\Resources\Resource;

/**
 * FuelAndEnergy resource for the Samsara API.
 *
 * Provides access to fuel and energy efficiency endpoints.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class FuelAndEnergyResource extends Resource
{
    /**
     * The API endpoint for this resource.
     */
    protected string $endpoint = '/fuel-energy';

    /**
     * Create a fuel purchase record.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function createFuelPurchase(array $data): array
    {
        $response = $this->client()->post('/fuel-purchases', $data);

        $this->handleError($response);

        return $response->json('data', $response->json());
    }

    /**
     * Get a query builder for driver efficiency.
     */
    public function driverEfficiency(): Builder
    {
        return $this->createBuilderWithEndpoint('/fuel-energy/driver-efficiency/stream');
    }

    /**
     * Get drivers fuel and energy report.
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public function driversFuelEnergyReport(array $params): array
    {
        $response = $this->client()->get('/fuel-energy/driver-reports', $params);

        $this->handleError($response);

        return $response->json();
    }

    /**
     * Get a query builder for vehicle efficiency.
     */
    public function vehicleEfficiency(): Builder
    {
        return $this->createBuilderWithEndpoint('/fuel-energy/vehicle-efficiency/stream');
    }

    /**
     * Get vehicles fuel and energy report.
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public function vehiclesFuelEnergyReport(array $params): array
    {
        $response = $this->client()->get('/fuel-energy/vehicle-reports', $params);

        $this->handleError($response);

        return $response->json();
    }
}
