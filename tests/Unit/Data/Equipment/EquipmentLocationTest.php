<?php

namespace Samsara\Tests\Unit\Data\Equipment;

use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\Equipment\EquipmentLocation;
use Samsara\Data\Equipment\EquipmentLocationResult;
use Samsara\Data\Equipment\EquipmentLocationSeries;

/**
 * Unit tests for the EquipmentLocation entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class EquipmentLocationTest extends TestCase
{
    #[Test]
    public function it_can_access_basic_properties(): void
    {
        $location = new EquipmentLocation([
            'latitude'  => 37.7749,
            'longitude' => -122.4194,
            'time'      => '2025-01-17T10:00:00Z',
            'heading'   => 180.0,
            'speed'     => 25.5,
        ]);

        $this->assertSame(37.7749, $location->latitude);
        $this->assertSame(-122.4194, $location->longitude);
        $this->assertSame('2025-01-17T10:00:00Z', $location->time);
        $this->assertSame(180.0, $location->heading);
        $this->assertSame(25.5, $location->speed);
    }

    #[Test]
    public function it_can_check_if_equipment_is_moving(): void
    {
        $moving = new EquipmentLocation([
            'speed' => 25.5,
        ]);

        $stationary = new EquipmentLocation([
            'speed' => 0.0,
        ]);

        $noSpeed = new EquipmentLocation([]);

        $this->assertTrue($moving->isMoving());
        $this->assertFalse($stationary->isMoving());
        $this->assertFalse($noSpeed->isMoving());
    }

    #[Test]
    public function it_can_convert_speed_to_kilometers(): void
    {
        $location = new EquipmentLocation([
            'speed' => 60.0,
        ]);

        $this->assertEqualsWithDelta(96.5604, $location->getSpeedKilometersPerHour(), 0.001);
    }

    #[Test]
    public function it_can_get_coordinates(): void
    {
        $location = new EquipmentLocation([
            'latitude'  => 37.7749,
            'longitude' => -122.4194,
        ]);

        $this->assertSame([
            'latitude'  => 37.7749,
            'longitude' => -122.4194,
        ], $location->getCoordinates());
        $this->assertSame([
            'latitude'  => null,
            'longitude' => null,
        ], (new EquipmentLocation)->getCoordinates());
    }

    #[Test]
    public function it_location_results_and_series_return_typed_locations(): void
    {
        $result = new EquipmentLocationResult([
            'location' => ['latitude' => 37.7749, 'longitude' => -122.4194, 'time' => '2026-08-12T12:00:00Z'],
        ]);
        $series = new EquipmentLocationSeries([
            'locations' => [
                ['latitude' => 37.7749, 'longitude' => -122.4194, 'time' => '2026-08-12T12:00:00Z'],
            ],
        ]);

        $this->assertInstanceOf(EquipmentLocation::class, $result->getLocation());
        $this->assertInstanceOf(EquipmentLocation::class, $series->getLocations()->first());
        $this->assertNull((new EquipmentLocationResult(['location' => null]))->getLocation());
        $this->assertTrue((new EquipmentLocationSeries(['locations' => null]))->getLocations()->isEmpty());
        $this->assertTrue((new EquipmentLocationSeries(['locations' => 'invalid']))->getLocations()->isEmpty());
    }

    #[Test]
    public function it_returns_null_for_missing_speed_conversion(): void
    {
        $location = new EquipmentLocation([]);

        $this->assertNull($location->getSpeedKilometersPerHour());
    }
}
