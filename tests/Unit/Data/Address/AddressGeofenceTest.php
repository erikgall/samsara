<?php

namespace Samsara\Tests\Unit\Data\Address;

use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\Address\AddressGeofence;

/**
 * Unit tests for the AddressGeofence entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class AddressGeofenceTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_circle(): void
    {
        $geofence = new AddressGeofence([
            'circle' => [
                'latitude'     => 37.765363,
                'longitude'    => -122.4029238,
                'radiusMeters' => 25,
            ],
        ]);

        $this->assertIsArray($geofence->circle);
        $this->assertSame(37.765363, $geofence->circle['latitude']);
        $this->assertSame(-122.4029238, $geofence->circle['longitude']);
        $this->assertSame(25, $geofence->circle['radiusMeters']);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $geofence = AddressGeofence::make([
            'circle' => [
                'radiusMeters' => 50,
            ],
        ]);

        $this->assertInstanceOf(AddressGeofence::class, $geofence);
    }

    #[Test]
    public function it_can_be_created_with_polygon(): void
    {
        $vertices = [
            ['latitude' => 37.765363, 'longitude' => -122.403098],
            ['latitude' => 38.765363, 'longitude' => -122.403098],
            ['latitude' => 37.765363, 'longitude' => -123.403098],
        ];

        $geofence = new AddressGeofence([
            'polygon' => [
                'vertices' => $vertices,
            ],
        ]);

        $this->assertIsArray($geofence->polygon);
        $this->assertCount(3, $geofence->polygon['vertices']);
    }

    #[Test]
    public function it_can_check_if_circle(): void
    {
        $geofence = new AddressGeofence([
            'circle' => ['radiusMeters' => 25],
        ]);

        $this->assertTrue($geofence->isCircle());
        $this->assertFalse($geofence->isPolygon());
    }

    #[Test]
    public function it_can_check_if_polygon(): void
    {
        $geofence = new AddressGeofence([
            'polygon' => [
                'vertices' => [
                    ['latitude' => 37.765363, 'longitude' => -122.403098],
                    ['latitude' => 38.765363, 'longitude' => -122.403098],
                    ['latitude' => 37.765363, 'longitude' => -123.403098],
                ],
            ],
        ]);

        $this->assertTrue($geofence->isPolygon());
        $this->assertFalse($geofence->isCircle());
    }

    #[Test]
    public function it_can_check_show_addresses_setting(): void
    {
        $geofence = new AddressGeofence([
            'settings' => [
                'showAddresses' => true,
            ],
        ]);

        $this->assertTrue($geofence->shouldShowAddresses());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'circle' => [
                'latitude'     => 37.765363,
                'longitude'    => -122.4029238,
                'radiusMeters' => 25,
            ],
        ];

        $geofence = new AddressGeofence($data);

        $this->assertSame($data, $geofence->toArray());
    }

    #[Test]
    public function it_can_get_center_for_circle(): void
    {
        $geofence = new AddressGeofence([
            'circle' => [
                'latitude'     => 37.765363,
                'longitude'    => -122.4029238,
                'radiusMeters' => 25,
            ],
        ]);

        $center = $geofence->getCenter();

        $this->assertIsArray($center);
        $this->assertSame(37.765363, $center['latitude']);
        $this->assertSame(-122.4029238, $center['longitude']);
    }

    #[Test]
    public function it_can_get_radius_for_circle(): void
    {
        $geofence = new AddressGeofence([
            'circle' => [
                'radiusMeters' => 100,
            ],
        ]);

        $this->assertSame(100, $geofence->getRadius());
    }

    #[Test]
    public function it_can_get_vertices_for_polygon(): void
    {
        $vertices = [
            ['latitude' => 37.765363, 'longitude' => -122.403098],
            ['latitude' => 38.765363, 'longitude' => -122.403098],
            ['latitude' => 37.765363, 'longitude' => -123.403098],
        ];

        $geofence = new AddressGeofence([
            'polygon' => [
                'vertices' => $vertices,
            ],
        ]);

        $this->assertSame($vertices, $geofence->getVertices());
    }

    #[Test]
    public function it_can_have_settings(): void
    {
        $geofence = new AddressGeofence([
            'settings' => [
                'showAddresses' => true,
            ],
        ]);

        $this->assertTrue($geofence->settings['showAddresses']);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $geofence = new AddressGeofence;

        $this->assertInstanceOf(Entity::class, $geofence);
    }

    #[Test]
    public function it_returns_empty_array_for_circle_vertices(): void
    {
        $geofence = new AddressGeofence([
            'circle' => [
                'radiusMeters' => 25,
            ],
        ]);

        $this->assertSame([], $geofence->getVertices());
    }

    #[Test]
    public function it_returns_false_for_both_when_empty(): void
    {
        $geofence = new AddressGeofence;

        $this->assertFalse($geofence->isCircle());
        $this->assertFalse($geofence->isPolygon());
    }

    #[Test]
    public function it_returns_false_when_show_addresses_not_set(): void
    {
        $geofence = new AddressGeofence;

        $this->assertFalse($geofence->shouldShowAddresses());
    }

    #[Test]
    public function it_returns_null_center_for_polygon(): void
    {
        $geofence = new AddressGeofence([
            'polygon' => [
                'vertices' => [],
            ],
        ]);

        $this->assertNull($geofence->getCenter());
    }

    #[Test]
    public function it_returns_null_radius_for_polygon(): void
    {
        $geofence = new AddressGeofence([
            'polygon' => [
                'vertices' => [],
            ],
        ]);

        $this->assertNull($geofence->getRadius());
    }
}
