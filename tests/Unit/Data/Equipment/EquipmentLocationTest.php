<?php

namespace Samsara\Tests\Unit\Data\Equipment;

use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\Equipment\EquipmentLocation;

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
    public function it_extends_entity(): void
    {
        $location = new EquipmentLocation([]);

        $this->assertInstanceOf(Entity::class, $location);
    }

    #[Test]
    public function it_returns_null_for_missing_speed_conversion(): void
    {
        $location = new EquipmentLocation([]);

        $this->assertNull($location->getSpeedKilometersPerHour());
    }
}
