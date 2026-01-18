<?php

namespace Samsara\Tests\Unit\Data\Driver;

use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use Samsara\Data\Driver\Driver;
use Samsara\Data\Driver\EldSettings;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\Driver\CarrierSettings;
use Samsara\Enums\DriverActivationStatus;
use Samsara\Data\Driver\StaticAssignedVehicle;

/**
 * Unit tests for the Driver entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class DriverTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $driver = new Driver([
            'id'       => '123',
            'name'     => 'Susan Jones',
            'username' => 'SusanJones',
            'phone'    => '5558234327',
        ]);

        $this->assertSame('123', $driver->id);
        $this->assertSame('Susan Jones', $driver->name);
        $this->assertSame('SusanJones', $driver->username);
        $this->assertSame('5558234327', $driver->phone);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $driver = Driver::make([
            'id'   => '123',
            'name' => 'Susan Jones',
        ]);

        $this->assertInstanceOf(Driver::class, $driver);
        $this->assertSame('123', $driver->getId());
    }

    #[Test]
    public function it_can_check_if_active(): void
    {
        $driver = new Driver([
            'driverActivationStatus' => 'active',
        ]);

        $this->assertTrue($driver->isActive());
        $this->assertFalse($driver->isDeactivated());
    }

    #[Test]
    public function it_can_check_if_deactivated(): void
    {
        $driver = new Driver([
            'driverActivationStatus' => 'deactivated',
        ]);

        $this->assertTrue($driver->isDeactivated());
        $this->assertFalse($driver->isActive());
    }

    #[Test]
    public function it_can_check_if_eld_exempt(): void
    {
        $driver = new Driver([
            'eldExempt' => true,
        ]);

        $this->assertTrue($driver->isEldExempt());
    }

    #[Test]
    public function it_can_check_if_has_static_assigned_vehicle(): void
    {
        $driver = new Driver([
            'staticAssignedVehicle' => [
                'id' => '123456789',
            ],
        ]);

        $this->assertTrue($driver->hasStaticAssignedVehicle());
    }

    #[Test]
    public function it_can_check_if_personal_conveyance_enabled(): void
    {
        $driver = new Driver([
            'eldPcEnabled' => true,
        ]);

        $this->assertTrue($driver->isPersonalConveyanceEnabled());
    }

    #[Test]
    public function it_can_check_if_yard_move_enabled(): void
    {
        $driver = new Driver([
            'eldYmEnabled' => true,
        ]);

        $this->assertTrue($driver->isYardMoveEnabled());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'id'       => '123',
            'name'     => 'Susan Jones',
            'username' => 'SusanJones',
        ];

        $driver = new Driver($data);

        $this->assertSame($data, $driver->toArray());
    }

    #[Test]
    public function it_can_get_activation_status_enum(): void
    {
        $driver = new Driver([
            'driverActivationStatus' => 'active',
        ]);

        $status = $driver->getActivationStatus();

        $this->assertInstanceOf(DriverActivationStatus::class, $status);
        $this->assertSame(DriverActivationStatus::ACTIVE, $status);
    }

    #[Test]
    public function it_can_get_carrier_settings_as_entity(): void
    {
        $driver = new Driver([
            'carrierSettings' => [
                'carrierName' => 'Acme Inc.',
            ],
        ]);

        $settings = $driver->getCarrierSettings();

        $this->assertInstanceOf(CarrierSettings::class, $settings);
    }

    #[Test]
    public function it_can_get_display_name(): void
    {
        $driver = new Driver([
            'name' => 'Susan Jones',
        ]);

        $this->assertSame('Susan Jones', $driver->getDisplayName());
    }

    #[Test]
    public function it_can_get_eld_settings_as_entity(): void
    {
        $driver = new Driver([
            'eldSettings' => [
                'rulesets' => [],
            ],
        ]);

        $settings = $driver->getEldSettings();

        $this->assertInstanceOf(EldSettings::class, $settings);
    }

    #[Test]
    public function it_can_get_external_id(): void
    {
        $driver = new Driver([
            'externalIds' => [
                'payrollId' => 'ABFS18600',
            ],
        ]);

        $this->assertSame('ABFS18600', $driver->getExternalId('payrollId'));
    }

    #[Test]
    public function it_can_get_static_assigned_vehicle_as_entity(): void
    {
        $driver = new Driver([
            'staticAssignedVehicle' => [
                'id'   => '123456789',
                'name' => 'Midwest Truck #4',
            ],
        ]);

        $vehicle = $driver->getStaticAssignedVehicle();

        $this->assertInstanceOf(StaticAssignedVehicle::class, $vehicle);
        $this->assertSame('123456789', $vehicle->id);
        $this->assertSame('Midwest Truck #4', $vehicle->name);
    }

    #[Test]
    public function it_can_get_tag_ids(): void
    {
        $driver = new Driver([
            'tags' => [
                ['id' => '3914', 'name' => 'East Coast'],
                ['id' => '4815', 'name' => 'West Coast'],
            ],
        ]);

        $this->assertSame(['3914', '4815'], $driver->getTagIds());
    }

    #[Test]
    public function it_can_have_carrier_settings(): void
    {
        $driver = new Driver([
            'carrierSettings' => [
                'carrierName' => 'Acme Inc.',
                'dotNumber'   => 98231,
            ],
        ]);

        $this->assertIsArray($driver->carrierSettings);
    }

    #[Test]
    public function it_can_have_created_at_time(): void
    {
        $driver = new Driver([
            'createdAtTime' => '2019-05-18T20:27:35Z',
        ]);

        $this->assertSame('2019-05-18T20:27:35Z', $driver->createdAtTime);
    }

    #[Test]
    public function it_can_have_current_id_card_code(): void
    {
        $driver = new Driver([
            'currentIdCardCode' => 'ABC123',
        ]);

        $this->assertSame('ABC123', $driver->currentIdCardCode);
    }

    #[Test]
    public function it_can_have_driver_activation_status(): void
    {
        $driver = new Driver([
            'driverActivationStatus' => 'active',
        ]);

        $this->assertSame('active', $driver->driverActivationStatus);
    }

    #[Test]
    public function it_can_have_eld_exempt_flag(): void
    {
        $driver = new Driver([
            'eldExempt'       => true,
            'eldExemptReason' => 'Short-haul exemption',
        ]);

        $this->assertTrue($driver->eldExempt);
        $this->assertSame('Short-haul exemption', $driver->eldExemptReason);
    }

    #[Test]
    public function it_can_have_eld_pc_enabled(): void
    {
        $driver = new Driver([
            'eldPcEnabled' => true,
        ]);

        $this->assertTrue($driver->eldPcEnabled);
    }

    #[Test]
    public function it_can_have_eld_settings(): void
    {
        $driver = new Driver([
            'eldSettings' => [
                'rulesets' => [],
            ],
        ]);

        $this->assertIsArray($driver->eldSettings);
    }

    #[Test]
    public function it_can_have_eld_ym_enabled(): void
    {
        $driver = new Driver([
            'eldYmEnabled' => true,
        ]);

        $this->assertTrue($driver->eldYmEnabled);
    }

    #[Test]
    public function it_can_have_external_ids(): void
    {
        $driver = new Driver([
            'externalIds' => [
                'maintenanceId' => '250020',
                'payrollId'     => 'ABFS18600',
            ],
        ]);

        $this->assertSame('250020', $driver->externalIds['maintenanceId']);
        $this->assertSame('ABFS18600', $driver->externalIds['payrollId']);
    }

    #[Test]
    public function it_can_have_license_info(): void
    {
        $driver = new Driver([
            'licenseNumber' => 'E1234567',
            'licenseState'  => 'CA',
        ]);

        $this->assertSame('E1234567', $driver->licenseNumber);
        $this->assertSame('CA', $driver->licenseState);
    }

    #[Test]
    public function it_can_have_locale(): void
    {
        $driver = new Driver([
            'locale' => 'en-US',
        ]);

        $this->assertSame('en-US', $driver->locale);
    }

    #[Test]
    public function it_can_have_notes(): void
    {
        $driver = new Driver([
            'notes' => 'Experienced long-haul driver',
        ]);

        $this->assertSame('Experienced long-haul driver', $driver->notes);
    }

    #[Test]
    public function it_can_have_profile_image_url(): void
    {
        $driver = new Driver([
            'profileImageUrl' => 'https://example.com/image.jpg',
        ]);

        $this->assertSame('https://example.com/image.jpg', $driver->profileImageUrl);
    }

    #[Test]
    public function it_can_have_static_assigned_vehicle(): void
    {
        $driver = new Driver([
            'staticAssignedVehicle' => [
                'id'   => '123456789',
                'name' => 'Midwest Truck #4',
            ],
        ]);

        $this->assertIsArray($driver->staticAssignedVehicle);
    }

    #[Test]
    public function it_can_have_tachograph_card_number(): void
    {
        $driver = new Driver([
            'tachographCardNumber' => 'T1234567890',
        ]);

        $this->assertSame('T1234567890', $driver->tachographCardNumber);
    }

    #[Test]
    public function it_can_have_tags(): void
    {
        $driver = new Driver([
            'tags' => [
                ['id' => '3914', 'name' => 'East Coast'],
            ],
        ]);

        $this->assertCount(1, $driver->tags);
        $this->assertSame('East Coast', $driver->tags[0]['name']);
    }

    #[Test]
    public function it_can_have_timezone(): void
    {
        $driver = new Driver([
            'timezone' => 'America/Los_Angeles',
        ]);

        $this->assertSame('America/Los_Angeles', $driver->timezone);
    }

    #[Test]
    public function it_can_have_updated_at_time(): void
    {
        $driver = new Driver([
            'updatedAtTime' => '2020-01-15T10:00:00Z',
        ]);

        $this->assertSame('2020-01-15T10:00:00Z', $driver->updatedAtTime);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $driver = new Driver;

        $this->assertInstanceOf(Entity::class, $driver);
    }

    #[Test]
    public function it_falls_back_to_username_for_display_name(): void
    {
        $driver = new Driver([
            'username' => 'SusanJones',
        ]);

        $this->assertSame('SusanJones', $driver->getDisplayName());
    }

    #[Test]
    public function it_returns_empty_array_when_no_tags(): void
    {
        $driver = new Driver;

        $this->assertSame([], $driver->getTagIds());
    }

    #[Test]
    public function it_returns_false_for_eld_exempt_when_not_set(): void
    {
        $driver = new Driver;

        $this->assertFalse($driver->isEldExempt());
    }

    #[Test]
    public function it_returns_false_when_no_static_assigned_vehicle(): void
    {
        $driver = new Driver;

        $this->assertFalse($driver->hasStaticAssignedVehicle());
    }

    #[Test]
    public function it_returns_null_activation_status_when_not_set(): void
    {
        $driver = new Driver;

        $this->assertNull($driver->getActivationStatus());
    }

    #[Test]
    public function it_returns_null_carrier_settings_when_not_set(): void
    {
        $driver = new Driver;

        $this->assertNull($driver->getCarrierSettings());
    }

    #[Test]
    public function it_returns_null_eld_settings_when_not_set(): void
    {
        $driver = new Driver;

        $this->assertNull($driver->getEldSettings());
    }

    #[Test]
    public function it_returns_null_for_missing_external_id(): void
    {
        $driver = new Driver([
            'externalIds' => [],
        ]);

        $this->assertNull($driver->getExternalId('payrollId'));
    }

    #[Test]
    public function it_returns_null_static_assigned_vehicle_when_not_set(): void
    {
        $driver = new Driver;

        $this->assertNull($driver->getStaticAssignedVehicle());
    }

    #[Test]
    public function it_returns_unknown_for_missing_display_name(): void
    {
        $driver = new Driver;

        $this->assertSame('Unknown', $driver->getDisplayName());
    }
}
