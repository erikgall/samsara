<?php

namespace Samsara\Tests\Unit\Data\Organization;

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
}
