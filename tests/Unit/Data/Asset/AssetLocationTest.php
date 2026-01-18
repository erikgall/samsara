<?php

namespace Samsara\Tests\Unit\Data\Asset;

use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use Samsara\Data\Asset\AssetLocation;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for the AssetLocation entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class AssetLocationTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $location = new AssetLocation([
            'latitude'          => 37.7749,
            'longitude'         => -122.4194,
            'headingDegrees'    => 90.0,
            'speedMilesPerHour' => 0.0,
            'time'              => '2024-04-10T07:06:25Z',
        ]);

        $this->assertSame(37.7749, $location->latitude);
        $this->assertSame(-122.4194, $location->longitude);
        $this->assertSame(90.0, $location->headingDegrees);
        $this->assertSame(0.0, $location->speedMilesPerHour);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $location = AssetLocation::make([
            'latitude' => 37.7749,
        ]);

        $this->assertInstanceOf(AssetLocation::class, $location);
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'latitude'  => 37.7749,
            'longitude' => -122.4194,
        ];

        $location = new AssetLocation($data);

        $this->assertSame($data, $location->toArray());
    }

    #[Test]
    public function it_can_get_coordinates(): void
    {
        $location = new AssetLocation([
            'latitude'  => 37.7749,
            'longitude' => -122.4194,
        ]);

        $coords = $location->getCoordinates();

        $this->assertSame(37.7749, $coords['latitude']);
        $this->assertSame(-122.4194, $coords['longitude']);
    }

    #[Test]
    public function it_can_have_address(): void
    {
        $location = new AssetLocation([
            'address' => [
                'name'             => 'Main Warehouse',
                'formattedAddress' => '123 Main St, San Francisco, CA',
            ],
        ]);

        $this->assertSame('Main Warehouse', $location->address['name']);
    }

    #[Test]
    public function it_can_have_asset(): void
    {
        $location = new AssetLocation([
            'asset' => [
                'id'   => 'asset-1',
                'name' => 'Forklift A',
            ],
        ]);

        $this->assertSame('asset-1', $location->asset['id']);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $location = new AssetLocation;

        $this->assertInstanceOf(Entity::class, $location);
    }

    #[Test]
    public function it_returns_null_coordinates_when_not_set(): void
    {
        $location = new AssetLocation;

        $coords = $location->getCoordinates();

        $this->assertNull($coords['latitude']);
        $this->assertNull($coords['longitude']);
    }
}
