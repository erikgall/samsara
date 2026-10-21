<?php

namespace Samsara\Tests\Unit\Resources\Additional;

use Samsara\Samsara;
use Samsara\Data\Entity;
use Samsara\Query\Builder;
use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\Factory as HttpFactory;
use Samsara\Resources\Additional\FuelAndEnergyResource;

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
        $this->http->preventStrayRequests();
        $this->samsara = new Samsara('test-token');
        $this->samsara->setHttpFactory($this->http);
    }

    #[Test]
    public function it_can_create_fuel_purchase(): void
    {
        $this->http->fake([
            '*fuel-purchase' => $this->http->response([
                'data' => [
                    'id'     => 'purchase-123',
                    'amount' => 50.0,
                ],
            ], 201),
        ]);

        $resource = new FuelAndEnergyResource($this->samsara);

        $result = $resource->createFuelPurchase([
            'fuelQuantityLiters'   => '189.27',
            'transactionLocation'  => '1 De Haro St, San Francisco, CA 94107',
            'transactionPrice'     => ['amount' => '196.45', 'currency' => 'usd'],
            'transactionReference' => 'INV-12345',
            'transactionTime'      => '2026-01-15T14:30:00Z',
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
    public function it_can_get_vehicle_efficiency_builder(): void
    {
        $resource = new FuelAndEnergyResource($this->samsara);

        $builder = $resource->vehicleEfficiency();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    #[Test]
    public function it_has_correct_endpoint(): void
    {
        $resource = new FuelAndEnergyResource($this->samsara);

        $this->assertSame('/fuel-purchase', $resource->getEndpoint());
    }

    #[Test]
    public function it_paginates_nested_driver_reports_without_an_unsupported_limit(): void
    {
        $this->http->fakeSequence()
            ->push([
                'data'       => ['driverReports' => [['id' => 'driver-report-1', 'driverId' => 'driver-1']]],
                'pagination' => ['endCursor' => 'drivers-next', 'hasNextPage' => true],
            ])
            ->push([
                'data'       => ['driverReports' => [['id' => 'driver-report-2', 'driverId' => 'driver-2']]],
                'pagination' => ['endCursor' => '', 'hasNextPage' => false],
            ]);

        $page = (new FuelAndEnergyResource($this->samsara))
            ->driversFuelEnergyReportQuery()
            ->where('startDate', '2024-01-01')
            ->where('endDate', '2024-01-31')
            ->paginate();
        $nextPage = $page->nextPage();

        $this->assertInstanceOf(Entity::class, $page->items()->first());
        $this->assertSame('driver-report-1', $page->items()->first()->getId());
        $this->assertSame('driver-report-2', $nextPage?->items()->first()->getId());
        $this->http->assertSentCount(2);
        $this->http->assertSent(fn ($request): bool => $request->data() === [
            'startDate' => '2024-01-01',
            'endDate'   => '2024-01-31',
            'after'     => 'drivers-next',
        ]);
    }

    #[Test]
    public function it_paginates_nested_vehicle_reports_without_an_unsupported_limit(): void
    {
        $this->http->fakeSequence()
            ->push([
                'data'       => ['vehicleReports' => [['id' => 'vehicle-report-1', 'vehicleId' => 'vehicle-1']]],
                'pagination' => ['endCursor' => 'vehicles-next', 'hasNextPage' => true],
            ])
            ->push([
                'data'       => ['vehicleReports' => [['id' => 'vehicle-report-2', 'vehicleId' => 'vehicle-2']]],
                'pagination' => ['endCursor' => '', 'hasNextPage' => false],
            ]);

        $page = (new FuelAndEnergyResource($this->samsara))
            ->vehiclesFuelEnergyReportQuery()
            ->where('startDate', '2024-01-01')
            ->where('endDate', '2024-01-31')
            ->paginate();
        $nextPage = $page->nextPage();

        $this->assertSame('vehicle-report-1', $page->items()->first()->getId());
        $this->assertSame('vehicle-report-2', $nextPage?->items()->first()->getId());
        $this->http->assertSentCount(2);
        $this->http->assertSent(fn ($request): bool => $request->data() === [
            'startDate' => '2024-01-01',
            'endDate'   => '2024-01-31',
            'after'     => 'vehicles-next',
        ]);
    }
}
