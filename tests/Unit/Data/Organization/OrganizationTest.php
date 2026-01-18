<?php

namespace Samsara\Tests\Unit\Data\Organization;

use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\Organization\Organization;

/**
 * Unit tests for the Organization entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class OrganizationTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $org = new Organization([
            'id'      => '12345',
            'name'    => 'Acme Trucking',
            'address' => '123 Main St, San Francisco, CA',
        ]);

        $this->assertSame('12345', $org->id);
        $this->assertSame('Acme Trucking', $org->name);
        $this->assertSame('123 Main St, San Francisco, CA', $org->address);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $org = Organization::make([
            'id' => '12345',
        ]);

        $this->assertInstanceOf(Organization::class, $org);
        $this->assertSame('12345', $org->getId());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'id'   => '12345',
            'name' => 'Acme Trucking',
        ];

        $org = new Organization($data);

        $this->assertSame($data, $org->toArray());
    }

    #[Test]
    public function it_can_have_carrier_settings(): void
    {
        $org = new Organization([
            'carrierSettings' => [
                'dotNumber' => '12345678',
                'mcNumber'  => 'MC-987654',
            ],
        ]);

        $this->assertSame('12345678', $org->carrierSettings['dotNumber']);
    }

    #[Test]
    public function it_can_have_settings(): void
    {
        $org = new Organization([
            'settings' => [
                'timezone' => 'America/Los_Angeles',
            ],
        ]);

        $this->assertSame('America/Los_Angeles', $org->settings['timezone']);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $org = new Organization;

        $this->assertInstanceOf(Entity::class, $org);
    }
}
