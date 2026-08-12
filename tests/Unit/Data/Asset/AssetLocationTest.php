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
            'asset'          => ['id' => 'asset-1'],
            'happenedAtTime' => '2024-04-10T07:06:25Z',
            'location'       => [
                'latitude'       => 37.7749,
                'longitude'      => -122.4194,
                'headingDegrees' => 90,
            ],
            'speed' => [
                'ecuSpeedMetersPerSecond' => 0,
                'gpsSpeedMetersPerSecond' => 12.5,
            ],
        ]);

        $this->assertSame('asset-1', $location->getAssetId());
        $this->assertSame(90, $location->getHeadingDegrees());
        $this->assertSame(0.0, $location->getEcuSpeedMetersPerSecond());
        $this->assertSame(12.5, $location->getGpsSpeedMetersPerSecond());
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
    public function it_can_have_nested_address_data(): void
    {
        $location = new AssetLocation([
            'location' => [
                'address' => [
                    'formattedAddress' => '123 Main St, San Francisco, CA',
                ],
            ],
        ]);

        $this->assertSame('123 Main St, San Francisco, CA', $location->location['address']['formattedAddress']);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $location = new AssetLocation;

        $this->assertInstanceOf(Entity::class, $location);
    }

    #[Test]
    public function it_reads_coordinates_from_the_current_location_wrapper(): void
    {
        $location = AssetLocation::make([
            'asset'          => ['id' => 'asset-1'],
            'happenedAtTime' => '2026-08-10T12:00:00Z',
            'location'       => [
                'latitude'  => 35.8456,
                'longitude' => -86.3903,
            ],
        ]);

        $this->assertSame([
            'latitude'  => 35.8456,
            'longitude' => -86.3903,
        ], $location->getCoordinates());
    }

    #[Test]
    public function it_returns_null_coordinates_when_not_set(): void
    {
        $location = new AssetLocation;

        $coords = $location->getCoordinates();

        $this->assertNull($coords['latitude']);
        $this->assertNull($coords['longitude']);
    }

    #[Test]
    public function it_returns_null_for_missing_or_malformed_nested_values(): void
    {
        $location = new AssetLocation([
            'asset'    => null,
            'location' => 'invalid',
            'speed'    => ['gpsSpeedMetersPerSecond' => 'fast'],
        ]);

        $this->assertNull($location->getAssetId());
        $this->assertNull($location->getHeadingDegrees());
        $this->assertNull($location->getEcuSpeedMetersPerSecond());
        $this->assertNull($location->getGpsSpeedMetersPerSecond());
    }

    #[Test]
    public function legacy_top_level_coordinates_remain_backwards_compatible(): void
    {
        $location = new AssetLocation([
            'latitude'  => 37.7749,
            'longitude' => -122.4194,
        ]);

        $coords = $location->getCoordinates();

        $this->assertSame(37.7749, $coords['latitude']);
        $this->assertSame(-122.4194, $coords['longitude']);
    }
}
