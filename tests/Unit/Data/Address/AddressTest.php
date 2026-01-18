<?php

namespace ErikGall\Samsara\Tests\Unit\Data\Address;

use ErikGall\Samsara\Data\Entity;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Data\Address\Address;
use ErikGall\Samsara\Data\Address\AddressGeofence;

/**
 * Unit tests for the Address entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class AddressTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $address = new Address([
            'id'               => '22408',
            'name'             => 'Samsara HQ',
            'formattedAddress' => '350 Rhode Island St, San Francisco, CA',
        ]);

        $this->assertSame('22408', $address->id);
        $this->assertSame('Samsara HQ', $address->name);
        $this->assertSame('350 Rhode Island St, San Francisco, CA', $address->formattedAddress);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $address = Address::make([
            'id'   => '22408',
            'name' => 'Samsara HQ',
        ]);

        $this->assertInstanceOf(Address::class, $address);
        $this->assertSame('22408', $address->getId());
    }

    #[Test]
    public function it_can_check_if_address_has_geofence(): void
    {
        $address = new Address([
            'geofence' => [
                'circle' => ['radiusMeters' => 25],
            ],
        ]);

        $this->assertTrue($address->hasGeofence());
    }

    #[Test]
    public function it_can_check_if_address_is_short_haul(): void
    {
        $address = new Address([
            'addressTypes' => ['shortHaul'],
        ]);

        $this->assertTrue($address->isShortHaul());
    }

    #[Test]
    public function it_can_check_if_address_is_yard(): void
    {
        $address = new Address([
            'addressTypes' => ['yard', 'shortHaul'],
        ]);

        $this->assertTrue($address->isYard());
    }

    #[Test]
    public function it_can_check_if_geofence_is_circle(): void
    {
        $address = new Address([
            'geofence' => [
                'circle' => ['radiusMeters' => 25],
            ],
        ]);

        $this->assertTrue($address->isCircleGeofence());
        $this->assertFalse($address->isPolygonGeofence());
    }

    #[Test]
    public function it_can_check_if_geofence_is_polygon(): void
    {
        $address = new Address([
            'geofence' => [
                'polygon' => [
                    'vertices' => [
                        ['latitude' => 37.765363, 'longitude' => -122.403098],
                        ['latitude' => 38.765363, 'longitude' => -122.403098],
                        ['latitude' => 37.765363, 'longitude' => -123.403098],
                    ],
                ],
            ],
        ]);

        $this->assertTrue($address->isPolygonGeofence());
        $this->assertFalse($address->isCircleGeofence());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'id'               => '22408',
            'name'             => 'Samsara HQ',
            'formattedAddress' => '350 Rhode Island St, San Francisco, CA',
        ];

        $address = new Address($data);

        $this->assertSame($data, $address->toArray());
    }

    #[Test]
    public function it_can_get_contact_ids(): void
    {
        $address = new Address([
            'contacts' => [
                ['id' => '1', 'firstName' => 'Jane'],
                ['id' => '2', 'firstName' => 'John'],
            ],
        ]);

        $this->assertSame(['1', '2'], $address->getContactIds());
    }

    #[Test]
    public function it_can_get_geofence_as_entity(): void
    {
        $address = new Address([
            'geofence' => [
                'circle' => [
                    'latitude'     => 37.765363,
                    'longitude'    => -122.4029238,
                    'radiusMeters' => 25,
                ],
            ],
        ]);

        $geofence = $address->getGeofence();

        $this->assertInstanceOf(AddressGeofence::class, $geofence);
    }

    #[Test]
    public function it_can_get_tag_ids(): void
    {
        $address = new Address([
            'tags' => [
                ['id' => '3914', 'name' => 'East Coast'],
                ['id' => '4815', 'name' => 'West Coast'],
            ],
        ]);

        $this->assertSame(['3914', '4815'], $address->getTagIds());
    }

    #[Test]
    public function it_can_have_address_types(): void
    {
        $address = new Address([
            'addressTypes' => ['yard', 'shortHaul'],
        ]);

        $this->assertSame(['yard', 'shortHaul'], $address->addressTypes);
    }

    #[Test]
    public function it_can_have_contacts(): void
    {
        $address = new Address([
            'contacts' => [
                ['id' => '1', 'firstName' => 'Jane', 'lastName' => 'Jones'],
                ['id' => '2', 'firstName' => 'John', 'lastName' => 'Doe'],
            ],
        ]);

        $this->assertCount(2, $address->contacts);
        $this->assertSame('Jane', $address->contacts[0]['firstName']);
    }

    #[Test]
    public function it_can_have_created_at_time(): void
    {
        $address = new Address([
            'createdAtTime' => '2019-05-18T20:27:35Z',
        ]);

        $this->assertSame('2019-05-18T20:27:35Z', $address->createdAtTime);
    }

    #[Test]
    public function it_can_have_external_ids(): void
    {
        $address = new Address([
            'externalIds' => [
                'maintenanceId' => '250020',
                'payrollId'     => 'ABFS18600',
            ],
        ]);

        $this->assertSame('250020', $address->externalIds['maintenanceId']);
        $this->assertSame('ABFS18600', $address->externalIds['payrollId']);
    }

    #[Test]
    public function it_can_have_geofence(): void
    {
        $address = new Address([
            'geofence' => [
                'circle' => [
                    'latitude'     => 37.765363,
                    'longitude'    => -122.4029238,
                    'radiusMeters' => 25,
                ],
            ],
        ]);

        $this->assertIsArray($address->geofence);
        $this->assertArrayHasKey('circle', $address->geofence);
    }

    #[Test]
    public function it_can_have_latitude_and_longitude(): void
    {
        $address = new Address([
            'latitude'  => 37.765363,
            'longitude' => -122.4029238,
        ]);

        $this->assertSame(37.765363, $address->latitude);
        $this->assertSame(-122.4029238, $address->longitude);
    }

    #[Test]
    public function it_can_have_notes(): void
    {
        $address = new Address([
            'notes' => 'Hours of operation: 8am - 6pm',
        ]);

        $this->assertSame('Hours of operation: 8am - 6pm', $address->notes);
    }

    #[Test]
    public function it_can_have_tags(): void
    {
        $address = new Address([
            'tags' => [
                ['id' => '3914', 'name' => 'East Coast'],
            ],
        ]);

        $this->assertCount(1, $address->tags);
        $this->assertSame('East Coast', $address->tags[0]['name']);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $address = new Address;

        $this->assertInstanceOf(Entity::class, $address);
    }

    #[Test]
    public function it_returns_empty_array_when_no_contacts(): void
    {
        $address = new Address;

        $this->assertSame([], $address->getContactIds());
    }

    #[Test]
    public function it_returns_empty_array_when_no_tags(): void
    {
        $address = new Address;

        $this->assertSame([], $address->getTagIds());
    }

    #[Test]
    public function it_returns_false_when_no_geofence(): void
    {
        $address = new Address;

        $this->assertFalse($address->hasGeofence());
    }

    #[Test]
    public function it_returns_false_when_not_yard(): void
    {
        $address = new Address([
            'addressTypes' => ['shortHaul'],
        ]);

        $this->assertFalse($address->isYard());
    }

    #[Test]
    public function it_returns_null_geofence_when_not_set(): void
    {
        $address = new Address;

        $this->assertNull($address->getGeofence());
    }
}
