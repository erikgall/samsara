<?php

namespace Samsara\Tests\Unit\Data\Trip;

use Samsara\Data\Entity;
use Samsara\Data\Trip\Trip;
use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for the Trip entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class TripTest extends TestCase
{
    protected const METERS_PER_MILE = 1609.344;

    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $trip = new Trip([
            'startTime'      => '2024-04-10T07:06:25Z',
            'endTime'        => '2024-04-10T09:30:00Z',
            'distanceMeters' => 160934,
            'drivingTimeMs'  => 8520000,
        ]);

        $this->assertSame('2024-04-10T07:06:25Z', $trip->startTime);
        $this->assertSame('2024-04-10T09:30:00Z', $trip->endTime);
        $this->assertSame(160934, $trip->distanceMeters);
        $this->assertSame(8520000, $trip->drivingTimeMs);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $trip = Trip::make([
            'startTime' => '2024-04-10T07:06:25Z',
        ]);

        $this->assertInstanceOf(Trip::class, $trip);
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'startTime'      => '2024-04-10T07:06:25Z',
            'endTime'        => '2024-04-10T09:30:00Z',
            'distanceMeters' => 160934,
        ];

        $trip = new Trip($data);

        $this->assertSame($data, $trip->toArray());
    }

    #[Test]
    public function it_can_get_distance_in_kilometers(): void
    {
        $trip = new Trip([
            'distanceMeters' => 100000,
        ]);

        $this->assertSame(100.0, $trip->getDistanceKilometers());
    }

    #[Test]
    public function it_can_get_distance_in_miles(): void
    {
        $trip = new Trip([
            'distanceMeters' => 160934,
        ]);

        $miles = $trip->getDistanceMiles();

        $this->assertEqualsWithDelta(100.0, $miles, 0.1);
    }

    #[Test]
    public function it_can_get_driving_time_hours(): void
    {
        $trip = new Trip([
            'drivingTimeMs' => 7200000, // 2 hours in ms
        ]);

        $this->assertSame(2.0, $trip->getDrivingTimeHours());
    }

    #[Test]
    public function it_can_get_driving_time_minutes(): void
    {
        $trip = new Trip([
            'drivingTimeMs' => 7200000, // 2 hours = 120 minutes in ms
        ]);

        $this->assertSame(120.0, $trip->getDrivingTimeMinutes());
    }

    #[Test]
    public function it_can_have_asset(): void
    {
        $trip = new Trip([
            'asset' => [
                'id'   => 'asset-1',
                'name' => 'Truck 42',
            ],
        ]);

        $this->assertSame('asset-1', $trip->asset['id']);
    }

    #[Test]
    public function it_can_have_driver(): void
    {
        $trip = new Trip([
            'driver' => [
                'id'   => 'driver-1',
                'name' => 'John Smith',
            ],
        ]);

        $this->assertSame('driver-1', $trip->driver['id']);
    }

    #[Test]
    public function it_can_have_end_location(): void
    {
        $trip = new Trip([
            'endLocation' => [
                'latitude'  => 37.7749,
                'longitude' => -122.4194,
            ],
        ]);

        $this->assertSame(37.7749, $trip->endLocation['latitude']);
    }

    #[Test]
    public function it_can_have_start_location(): void
    {
        $trip = new Trip([
            'startLocation' => [
                'latitude'  => 34.0522,
                'longitude' => -118.2437,
            ],
        ]);

        $this->assertSame(34.0522, $trip->startLocation['latitude']);
    }

    #[Test]
    public function it_can_have_vehicle(): void
    {
        $trip = new Trip([
            'vehicle' => [
                'id'   => 'vehicle-1',
                'name' => 'Truck 42',
            ],
        ]);

        $this->assertSame('vehicle-1', $trip->vehicle['id']);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $trip = new Trip;

        $this->assertInstanceOf(Entity::class, $trip);
    }

    #[Test]
    public function it_returns_null_distance_when_not_set(): void
    {
        $trip = new Trip;

        $this->assertNull($trip->getDistanceMiles());
        $this->assertNull($trip->getDistanceKilometers());
    }

    #[Test]
    public function it_returns_null_driving_time_when_not_set(): void
    {
        $trip = new Trip;

        $this->assertNull($trip->getDrivingTimeHours());
        $this->assertNull($trip->getDrivingTimeMinutes());
    }
}
