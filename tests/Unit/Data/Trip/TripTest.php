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
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $trip = new Trip([
            'tripStartTime'    => '2024-04-10T07:06:25Z',
            'tripEndTime'      => '2024-04-10T09:30:00Z',
            'createdAtTime'    => '2024-04-10T07:07:00Z',
            'updatedAtTime'    => '2024-04-10T09:31:00Z',
            'completionStatus' => 'completed',
        ]);

        $this->assertSame('2024-04-10T07:06:25Z', $trip->tripStartTime);
        $this->assertSame('2024-04-10T09:30:00Z', $trip->tripEndTime);
        $this->assertSame('2024-04-10T07:07:00Z', $trip->createdAtTime);
        $this->assertSame('2024-04-10T09:31:00Z', $trip->updatedAtTime);
        $this->assertSame('completed', $trip->completionStatus);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $trip = Trip::make([
            'tripStartTime' => '2024-04-10T07:06:25Z',
        ]);

        $this->assertInstanceOf(Trip::class, $trip);
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'tripStartTime' => '2024-04-10T07:06:25Z',
            'tripEndTime'   => '2024-04-10T09:30:00Z',
        ];

        $trip = new Trip($data);

        $this->assertSame($data, $trip->toArray());
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
    public function it_does_not_expose_helpers_for_fields_absent_from_the_current_contract(): void
    {
        $trip = new Trip;

        $this->assertFalse(method_exists($trip, 'getDistanceMiles'));
        $this->assertFalse(method_exists($trip, 'getDistanceKilometers'));
        $this->assertFalse(method_exists($trip, 'getDrivingTimeHours'));
        $this->assertFalse(method_exists($trip, 'getDrivingTimeMinutes'));
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $trip = new Trip;

        $this->assertInstanceOf(Entity::class, $trip);
    }
}
