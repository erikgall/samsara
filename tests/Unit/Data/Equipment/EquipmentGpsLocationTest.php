<?php

namespace Samsara\Tests\Unit\Data\Equipment;

use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\Equipment\EquipmentGpsLocation;

/**
 * Unit tests for equipment GPS locations.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class EquipmentGpsLocationTest extends TestCase
{
    #[Test]
    public function it_gets_the_address_name(): void
    {
        $location = new EquipmentGpsLocation([
            'address' => ['name' => 'Main Distribution Warehouse'],
        ]);

        $this->assertSame('Main Distribution Warehouse', $location->getAddressName());
        $this->assertSame('', (new EquipmentGpsLocation(['address' => ['name' => '']]))->getAddressName());
    }

    #[Test]
    public function it_gets_the_reverse_geocoded_location(): void
    {
        $location = new EquipmentGpsLocation([
            'reverseGeo' => ['formattedLocation' => '16 N Fair Oaks Ave, Pasadena, CA 91103'],
        ]);

        $this->assertSame('16 N Fair Oaks Ave, Pasadena, CA 91103', $location->getReverseGeoLocation());
        $this->assertSame('', (new EquipmentGpsLocation(['reverseGeo' => ['formattedLocation' => '']]))->getReverseGeoLocation());
    }

    #[Test]
    public function it_returns_null_for_a_missing_or_malformed_address_name(): void
    {
        $locations = [
            new EquipmentGpsLocation,
            new EquipmentGpsLocation(['address' => null]),
            new EquipmentGpsLocation(['address' => 'malformed']),
            new EquipmentGpsLocation(['address' => []]),
            new EquipmentGpsLocation(['address' => ['id' => '123']]),
            new EquipmentGpsLocation(['address' => ['name' => null]]),
            new EquipmentGpsLocation(['address' => ['name' => 123]]),
        ];

        foreach ($locations as $location) {
            $this->assertNull($location->getAddressName());
        }
    }

    #[Test]
    public function it_returns_null_for_a_missing_or_malformed_reverse_geocoded_location(): void
    {
        $locations = [
            new EquipmentGpsLocation,
            new EquipmentGpsLocation(['reverseGeo' => null]),
            new EquipmentGpsLocation(['reverseGeo' => 'malformed']),
            new EquipmentGpsLocation(['reverseGeo' => []]),
            new EquipmentGpsLocation(['reverseGeo' => ['other' => 'value']]),
            new EquipmentGpsLocation(['reverseGeo' => ['formattedLocation' => null]]),
            new EquipmentGpsLocation(['reverseGeo' => ['formattedLocation' => 123]]),
        ];

        foreach ($locations as $location) {
            $this->assertNull($location->getReverseGeoLocation());
        }
    }
}
