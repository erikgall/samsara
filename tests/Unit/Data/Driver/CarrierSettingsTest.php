<?php

namespace Samsara\Tests\Unit\Data\Driver;

use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\Driver\CarrierSettings;

/**
 * Unit tests for the CarrierSettings entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class CarrierSettingsTest extends TestCase
{
    #[Test]
    public function it_can_get_carrier_name(): void
    {
        $settings = new CarrierSettings([
            'carrierName' => 'Acme Inc.',
        ]);

        $this->assertSame('Acme Inc.', $settings->getCarrierName());
    }

    #[Test]
    public function it_can_get_dot_number(): void
    {
        $settings = new CarrierSettings([
            'dotNumber' => 98231,
        ]);

        $this->assertSame(98231, $settings->getDotNumber());
    }

    #[Test]
    public function it_can_have_home_terminal_address(): void
    {
        $settings = new CarrierSettings([
            'homeTerminalAddress' => '123 Main St, Springfield, IL 62701',
        ]);

        $this->assertSame('123 Main St, Springfield, IL 62701', $settings->homeTerminalAddress);
    }

    #[Test]
    public function it_can_have_home_terminal_name(): void
    {
        $settings = new CarrierSettings([
            'homeTerminalName' => 'Springfield Terminal',
        ]);

        $this->assertSame('Springfield Terminal', $settings->homeTerminalName);
    }

    #[Test]
    public function it_can_have_main_office_address(): void
    {
        $settings = new CarrierSettings([
            'mainOfficeAddress' => '1234 Pear St., Scranton, PA 62814',
        ]);

        $this->assertSame('1234 Pear St., Scranton, PA 62814', $settings->mainOfficeAddress);
    }

    #[Test]
    public function it_returns_null_for_missing_carrier_name(): void
    {
        $settings = new CarrierSettings;

        $this->assertNull($settings->getCarrierName());
    }

    #[Test]
    public function it_returns_null_for_missing_dot_number(): void
    {
        $settings = new CarrierSettings;

        $this->assertNull($settings->getDotNumber());
    }
}
