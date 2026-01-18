<?php

namespace Samsara\Tests\Unit\Data\Vehicle;

use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use Samsara\Data\Vehicle\GpsLocation;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for the GpsLocation entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class GpsLocationTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $gps = new GpsLocation([
            'latitude'          => 37.765363,
            'longitude'         => -122.4029238,
            'headingDegrees'    => 180,
            'speedMilesPerHour' => 65.5,
            'time'              => '2024-01-15T10:00:00Z',
        ]);

        $this->assertSame(37.765363, $gps->latitude);
        $this->assertSame(-122.4029238, $gps->longitude);
        $this->assertSame(180, $gps->headingDegrees);
        $this->assertSame(65.5, $gps->speedMilesPerHour);
        $this->assertSame('2024-01-15T10:00:00Z', $gps->time);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $gps = GpsLocation::make([
            'latitude'  => 37.765363,
            'longitude' => -122.4029238,
        ]);

        $this->assertInstanceOf(GpsLocation::class, $gps);
    }

    #[Test]
    public function it_can_check_if_ecu_speed(): void
    {
        $gps = new GpsLocation([
            'isEcuSpeed' => true,
        ]);

        $this->assertTrue($gps->isEcuSpeed);
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'latitude'  => 37.765363,
            'longitude' => -122.4029238,
            'time'      => '2024-01-15T10:00:00Z',
        ];

        $gps = new GpsLocation($data);

        $this->assertSame($data, $gps->toArray());
    }

    #[Test]
    public function it_can_get_coordinates_array(): void
    {
        $gps = new GpsLocation([
            'latitude'  => 37.765363,
            'longitude' => -122.4029238,
        ]);

        $coords = $gps->getCoordinates();

        $this->assertSame(37.765363, $coords['latitude']);
        $this->assertSame(-122.4029238, $coords['longitude']);
    }

    #[Test]
    public function it_can_get_speed_in_kilometers_per_hour(): void
    {
        $gps = new GpsLocation([
            'speedMilesPerHour' => 62.137,
        ]);

        // 62.137 mph is approximately 100 km/h
        $this->assertEqualsWithDelta(100.0, $gps->getSpeedKilometersPerHour(), 0.1);
    }

    #[Test]
    public function it_can_have_address(): void
    {
        $gps = new GpsLocation([
            'address' => '350 Rhode Island St, San Francisco, CA',
        ]);

        $this->assertSame('350 Rhode Island St, San Francisco, CA', $gps->address);
    }

    #[Test]
    public function it_can_have_decorations(): void
    {
        $gps = new GpsLocation([
            'decorations' => [
                'someDecoration' => 'value',
            ],
        ]);

        $this->assertIsArray($gps->decorations);
    }

    #[Test]
    public function it_can_have_reverse_geo(): void
    {
        $gps = new GpsLocation([
            'reverseGeo' => [
                'formattedLocation' => '350 Rhode Island St, San Francisco, CA',
            ],
        ]);

        $this->assertIsArray($gps->reverseGeo);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $gps = new GpsLocation;

        $this->assertInstanceOf(Entity::class, $gps);
    }

    #[Test]
    public function it_returns_null_for_speed_when_not_set(): void
    {
        $gps = new GpsLocation;

        $this->assertNull($gps->getSpeedKilometersPerHour());
    }
}
