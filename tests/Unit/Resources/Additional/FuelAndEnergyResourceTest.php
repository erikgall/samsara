<?php

namespace ErikGall\Samsara\Tests\Unit\Resources\Additional;

use ErikGall\Samsara\Samsara;
use ErikGall\Samsara\Query\Builder;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\Factory as HttpFactory;
use ErikGall\Samsara\Resources\Additional\FuelAndEnergyResource;

/**
 * Unit tests for the FuelAndEnergyResource class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class FuelAndEnergyResourceTest extends TestCase
{
    protected HttpFactory $http;

    protected Samsara $samsara;

    protected function setUp(): void
    {
        parent::setUp();

        $this->http = new HttpFactory;
        $this->samsara = new Samsara('test-token');
        $this->samsara->setHttpFactory($this->http);
    }

    #[Test]
    public function it_can_create_fuel_purchase(): void
    {
        $this->http->fake([
            '*fuel-purchases' => $this->http->response([
                'data' => [
                    'id'     => 'purchase-123',
                    'amount' => 50.0,
                ],
            ], 201),
        ]);

        $resource = new FuelAndEnergyResource($this->samsara);

        $result = $resource->createFuelPurchase([
            'vehicleId' => 'vehicle-1',
            'amount'    => 50.0,
        ]);

        $this->assertIsArray($result);
        $this->assertSame('purchase-123', $result['id']);
    }

    #[Test]
    public function it_can_get_driver_efficiency_builder(): void
    {
        $resource = new FuelAndEnergyResource($this->samsara);

        $builder = $resource->driverEfficiency();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_get_drivers_fuel_energy_report(): void
    {
        $this->http->fake([
            '*fuel-energy/driver-reports*' => $this->http->response([
                'data' => [
                    ['driverId' => 'driver-1', 'totalFuel' => 100],
                ],
            ], 200),
        ]);

        $resource = new FuelAndEnergyResource($this->samsara);

        $result = $resource->driversFuelEnergyReport(['startTime' => '2024-01-01']);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
    }

    #[Test]
    public function it_can_get_vehicle_efficiency_builder(): void
    {
        $resource = new FuelAndEnergyResource($this->samsara);

        $builder = $resource->vehicleEfficiency();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_can_get_vehicles_fuel_energy_report(): void
    {
        $this->http->fake([
            '*fuel-energy/vehicle-reports*' => $this->http->response([
                'data' => [
                    ['vehicleId' => 'vehicle-1', 'totalFuel' => 500],
                ],
            ], 200),
        ]);

        $resource = new FuelAndEnergyResource($this->samsara);

        $result = $resource->vehiclesFuelEnergyReport(['startTime' => '2024-01-01']);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new FuelAndEnergyResource($this->samsara);

        $this->assertSame('/fuel-energy', $resource->getEndpoint());
    }
}
