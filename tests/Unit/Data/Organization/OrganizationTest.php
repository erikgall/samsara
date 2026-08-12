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
            'id'   => '12345',
            'name' => 'Acme Trucking',
        ]);

        $this->assertSame('12345', $org->id);
        $this->assertSame('Acme Trucking', $org->name);
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
    public function it_can_have_a_main_office_address(): void
    {
        $org = new Organization([
            'carrierSettings' => [
                'mainOfficeAddress' => '123 Main St, San Francisco, CA',
            ],
        ]);

        $this->assertSame('123 Main St, San Francisco, CA', $org->carrierSettings['mainOfficeAddress']);
    }

    #[Test]
    public function it_can_have_carrier_settings(): void
    {
        $org = new Organization([
            'carrierSettings' => [
                'carrierName'       => 'Acme Trucking',
                'dotNumber'         => 12345678,
                'mainOfficeAddress' => '123 Main St, San Francisco, CA',
            ],
        ]);

        $this->assertSame(12345678, $org->carrierSettings['dotNumber']);
        $this->assertSame('Acme Trucking', $org->carrierSettings['carrierName']);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $org = new Organization;

        $this->assertInstanceOf(Entity::class, $org);
    }
}
