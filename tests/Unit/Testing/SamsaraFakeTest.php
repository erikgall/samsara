<?php

namespace ErikGall\Samsara\Tests\Unit\Testing;

use ErikGall\Samsara\Samsara;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Testing\SamsaraFake;
use PHPUnit\Framework\ExpectationFailedException;

/**
 * Unit tests for the SamsaraFake class.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class SamsaraFakeTest extends TestCase
{
    #[Test]
    public function it_can_assert_nothing_requested(): void
    {
        $fake = SamsaraFake::create();

        $fake->assertNothingRequested();
    }

    #[Test]
    public function it_can_assert_requested(): void
    {
        $fake = SamsaraFake::create();

        $fake->fakeDrivers([['id' => 'driver-1']]);
        $fake->drivers()->all();

        $fake->assertRequested('/fleet/drivers');
    }

    #[Test]
    public function it_can_create_fake_instance(): void
    {
        $fake = SamsaraFake::create();

        $this->assertInstanceOf(SamsaraFake::class, $fake);
        $this->assertInstanceOf(Samsara::class, $fake);
    }

    #[Test]
    public function it_can_fake_drivers(): void
    {
        $fake = SamsaraFake::create();

        $fake->fakeDrivers([
            ['id' => 'driver-1', 'name' => 'John Doe'],
        ]);

        $drivers = $fake->drivers()->all();

        $this->assertCount(1, $drivers);
        $this->assertSame('John Doe', $drivers[0]->name);
    }

    #[Test]
    public function it_can_fake_empty_response(): void
    {
        $fake = SamsaraFake::create();

        $fake->fakeResponse('/fleet/drivers', []);

        $drivers = $fake->drivers()->all();

        $this->assertCount(0, $drivers);
    }

    #[Test]
    public function it_can_fake_response_for_endpoint(): void
    {
        $fake = SamsaraFake::create();

        $fake->fakeResponse('/fleet/drivers', [
            ['id' => 'driver-1', 'name' => 'John Doe'],
            ['id' => 'driver-2', 'name' => 'Jane Doe'],
        ]);

        $drivers = $fake->drivers()->all();

        $this->assertCount(2, $drivers);
        $this->assertSame('driver-1', $drivers[0]->id);
    }

    #[Test]
    public function it_can_fake_response_with_status_code(): void
    {
        $fake = SamsaraFake::create();

        $fake->fakeResponse('/fleet/drivers', [
            ['id' => 'driver-1'],
        ], 201);

        $drivers = $fake->drivers()->all();

        $this->assertCount(1, $drivers);
    }

    #[Test]
    public function it_can_fake_single_resource_response(): void
    {
        $fake = SamsaraFake::create();

        $fake->fakeResponse('/fleet/drivers/driver-123', [
            'id'   => 'driver-123',
            'name' => 'John Doe',
        ]);

        $driver = $fake->drivers()->find('driver-123');

        $this->assertSame('driver-123', $driver->id);
        $this->assertSame('John Doe', $driver->name);
    }

    #[Test]
    public function it_can_fake_vehicle_stats(): void
    {
        $fake = SamsaraFake::create();

        $fake->fakeVehicleStats([
            ['id' => 'vehicle-1', 'name' => 'Truck 1', 'gps' => ['latitude' => 37.4, 'longitude' => -122.0]],
        ]);

        $stats = $fake->vehicleStats()->current()->get();

        $this->assertCount(1, $stats);
    }

    #[Test]
    public function it_can_fake_vehicles(): void
    {
        $fake = SamsaraFake::create();

        $fake->fakeVehicles([
            ['id' => 'vehicle-1', 'name' => 'Truck 1'],
        ]);

        $vehicles = $fake->vehicles()->all();

        $this->assertCount(1, $vehicles);
        $this->assertSame('Truck 1', $vehicles[0]->name);
    }

    #[Test]
    public function it_fails_when_asserting_endpoint_not_requested(): void
    {
        $fake = SamsaraFake::create();

        $fake->fakeDrivers([['id' => 'driver-1']]);
        $fake->drivers()->all();

        $this->expectException(ExpectationFailedException::class);

        $fake->assertRequested('/fleet/vehicles');
    }

    #[Test]
    public function it_fails_when_asserting_nothing_requested_but_has_requests(): void
    {
        $fake = SamsaraFake::create();

        $fake->fakeDrivers([['id' => 'driver-1']]);
        $fake->drivers()->all();

        $this->expectException(ExpectationFailedException::class);

        $fake->assertNothingRequested();
    }

    #[Test]
    public function it_records_requests(): void
    {
        $fake = SamsaraFake::create();

        $fake->fakeDrivers([['id' => 'driver-1']]);
        $fake->fakeVehicles([['id' => 'vehicle-1']]);

        $fake->drivers()->all();
        $fake->vehicles()->all();

        $requests = $fake->getRecordedRequests();

        $this->assertCount(2, $requests);
    }
}
