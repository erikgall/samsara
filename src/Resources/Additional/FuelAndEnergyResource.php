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

        return $response->json('data', $response->json());
    }

    /**
     * Get a query builder for driver efficiency.
     */
    public function driverEfficiency(): Builder
    {
        return $this->createBuilderWithEndpoint('/driver-efficiency/drivers')
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
        $endpoint = '/fleet/reports/drivers/fuel-energy';
        $response = $this->executeSdkRequest(
            fn () => $this->client()->get($endpoint, $params),
            $endpoint
        );

        return $response->json();
    }

    /**
     * Get a query builder for vehicle efficiency.
     */
    public function vehicleEfficiency(): Builder
    {
        return $this->createBuilderWithEndpoint('/driver-efficiency/vehicles')
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
        $response = $this->executeSdkRequest(
            fn () => $this->client()->get($endpoint, $params),
            $endpoint
        );

        return $response->json();
    }

    /**
     * {@inheritDoc}
     */
    protected function supportedGenericOperations(): array
    {
        return [];
    }
}
