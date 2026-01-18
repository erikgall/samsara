<?php

namespace ErikGall\Samsara\Tests\Unit\Data\Vehicle;

use ErikGall\Samsara\Data\Entity;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Data\Vehicle\Gateway;
use ErikGall\Samsara\Data\Vehicle\Vehicle;
use ErikGall\Samsara\Data\Vehicle\StaticAssignedDriver;

/**
 * Unit tests for the Vehicle entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class VehicleTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $vehicle = new Vehicle([
            'id'    => '112',
            'name'  => 'Truck A7',
            'vin'   => '1FUJA6BD31LJ09646',
            'make'  => 'Ford',
            'model' => 'F150',
        ]);

        $this->assertSame('112', $vehicle->id);
        $this->assertSame('Truck A7', $vehicle->name);
        $this->assertSame('1FUJA6BD31LJ09646', $vehicle->vin);
        $this->assertSame('Ford', $vehicle->make);
        $this->assertSame('F150', $vehicle->model);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $vehicle = Vehicle::make([
            'id'   => '112',
            'name' => 'Truck A7',
        ]);

        $this->assertInstanceOf(Vehicle::class, $vehicle);
        $this->assertSame('112', $vehicle->getId());
    }

    #[Test]
    public function it_can_check_if_has_static_assigned_driver(): void
    {
        $vehicle = new Vehicle([
            'staticAssignedDriver' => [
                'id' => '88668',
            ],
        ]);

        $this->assertTrue($vehicle->hasStaticAssignedDriver());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'id'   => '112',
            'name' => 'Truck A7',
            'vin'  => '1FUJA6BD31LJ09646',
        ];

        $vehicle = new Vehicle($data);

        $this->assertSame($data, $vehicle->toArray());
    }

    #[Test]
    public function it_can_get_display_name(): void
    {
        $vehicle = new Vehicle([
            'name' => 'Truck A7',
        ]);

        $this->assertSame('Truck A7', $vehicle->getDisplayName());
    }

    #[Test]
    public function it_can_get_external_id(): void
    {
        $vehicle = new Vehicle([
            'externalIds' => [
                'fleetId' => 'F12345',
            ],
        ]);

        $this->assertSame('F12345', $vehicle->getExternalId('fleetId'));
    }

    #[Test]
    public function it_can_get_gateway_as_entity(): void
    {
        $vehicle = new Vehicle([
            'gateway' => [
                'serial' => 'ABCD-123-XYZ',
                'model'  => 'VG34',
            ],
        ]);

        $gateway = $vehicle->getGateway();

        $this->assertInstanceOf(Gateway::class, $gateway);
        $this->assertSame('ABCD-123-XYZ', $gateway->serial);
        $this->assertSame('VG34', $gateway->model);
    }

    #[Test]
    public function it_can_get_static_assigned_driver_as_entity(): void
    {
        $vehicle = new Vehicle([
            'staticAssignedDriver' => [
                'id'   => '88668',
                'name' => 'Susan Bob',
            ],
        ]);

        $driver = $vehicle->getStaticAssignedDriver();

        $this->assertInstanceOf(StaticAssignedDriver::class, $driver);
        $this->assertSame('88668', $driver->id);
        $this->assertSame('Susan Bob', $driver->name);
    }

    #[Test]
    public function it_can_get_tag_ids(): void
    {
        $vehicle = new Vehicle([
            'tags' => [
                ['id' => '3914', 'name' => 'East Coast'],
                ['id' => '4815', 'name' => 'West Coast'],
            ],
        ]);

        $this->assertSame(['3914', '4815'], $vehicle->getTagIds());
    }

    #[Test]
    public function it_can_have_attributes(): void
    {
        $vehicle = new Vehicle([
            'attributes' => [
                ['id' => 'attr1', 'name' => 'Color', 'value' => 'Red'],
            ],
        ]);

        $this->assertCount(1, $vehicle->attributes);
    }

    #[Test]
    public function it_can_have_camera_serial(): void
    {
        $vehicle = new Vehicle([
            'cameraSerial' => 'CAM123456',
        ]);

        $this->assertSame('CAM123456', $vehicle->cameraSerial);
    }

    #[Test]
    public function it_can_have_esn(): void
    {
        $vehicle = new Vehicle([
            'esn' => 'ESN12345',
        ]);

        $this->assertSame('ESN12345', $vehicle->esn);
    }

    #[Test]
    public function it_can_have_external_ids(): void
    {
        $vehicle = new Vehicle([
            'externalIds' => [
                'fleetId'    => 'F12345',
                'unitNumber' => 'UN001',
            ],
        ]);

        $this->assertSame('F12345', $vehicle->externalIds['fleetId']);
        $this->assertSame('UN001', $vehicle->externalIds['unitNumber']);
    }

    #[Test]
    public function it_can_have_gateway(): void
    {
        $vehicle = new Vehicle([
            'gateway' => [
                'serial' => 'ABCD-123-XYZ',
                'model'  => 'VG34',
            ],
        ]);

        $this->assertIsArray($vehicle->gateway);
        $this->assertSame('ABCD-123-XYZ', $vehicle->gateway['serial']);
    }

    #[Test]
    public function it_can_have_gross_vehicle_weight(): void
    {
        $vehicle = new Vehicle([
            'grossVehicleWeight' => 26000,
        ]);

        $this->assertSame(26000, $vehicle->grossVehicleWeight);
    }

    #[Test]
    public function it_can_have_harsh_acceleration_setting_type(): void
    {
        $vehicle = new Vehicle([
            'harshAccelerationSettingType' => 'passenger',
        ]);

        $this->assertSame('passenger', $vehicle->harshAccelerationSettingType);
    }

    #[Test]
    public function it_can_have_license_plate(): void
    {
        $vehicle = new Vehicle([
            'licensePlate' => 'XHK1234',
        ]);

        $this->assertSame('XHK1234', $vehicle->licensePlate);
    }

    #[Test]
    public function it_can_have_notes(): void
    {
        $vehicle = new Vehicle([
            'notes' => 'Primary delivery truck',
        ]);

        $this->assertSame('Primary delivery truck', $vehicle->notes);
    }

    #[Test]
    public function it_can_have_serial(): void
    {
        $vehicle = new Vehicle([
            'serial' => 'ABCD-123-XYZ',
        ]);

        $this->assertSame('ABCD-123-XYZ', $vehicle->serial);
    }

    #[Test]
    public function it_can_have_static_assigned_driver(): void
    {
        $vehicle = new Vehicle([
            'staticAssignedDriver' => [
                'id'   => '88668',
                'name' => 'Susan Bob',
            ],
        ]);

        $this->assertIsArray($vehicle->staticAssignedDriver);
    }

    #[Test]
    public function it_can_have_tags(): void
    {
        $vehicle = new Vehicle([
            'tags' => [
                ['id' => '3914', 'name' => 'East Coast'],
            ],
        ]);

        $this->assertCount(1, $vehicle->tags);
        $this->assertSame('East Coast', $vehicle->tags[0]['name']);
    }

    #[Test]
    public function it_can_have_vehicle_regulation_mode(): void
    {
        $vehicle = new Vehicle([
            'vehicleRegulationMode' => 'regulated',
        ]);

        $this->assertSame('regulated', $vehicle->vehicleRegulationMode);
    }

    #[Test]
    public function it_can_have_vehicle_type(): void
    {
        $vehicle = new Vehicle([
            'vehicleType' => 'truck',
        ]);

        $this->assertSame('truck', $vehicle->vehicleType);
    }

    #[Test]
    public function it_can_have_year(): void
    {
        $vehicle = new Vehicle([
            'year' => '2008',
        ]);

        $this->assertSame('2008', $vehicle->year);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $vehicle = new Vehicle;

        $this->assertInstanceOf(Entity::class, $vehicle);
    }

    #[Test]
    public function it_returns_empty_array_when_no_tags(): void
    {
        $vehicle = new Vehicle;

        $this->assertSame([], $vehicle->getTagIds());
    }

    #[Test]
    public function it_returns_false_when_no_static_assigned_driver(): void
    {
        $vehicle = new Vehicle;

        $this->assertFalse($vehicle->hasStaticAssignedDriver());
    }

    #[Test]
    public function it_returns_null_for_missing_external_id(): void
    {
        $vehicle = new Vehicle([
            'externalIds' => [],
        ]);

        $this->assertNull($vehicle->getExternalId('fleetId'));
    }

    #[Test]
    public function it_returns_null_gateway_when_not_set(): void
    {
        $vehicle = new Vehicle;

        $this->assertNull($vehicle->getGateway());
    }

    #[Test]
    public function it_returns_null_static_assigned_driver_when_not_set(): void
    {
        $vehicle = new Vehicle;

        $this->assertNull($vehicle->getStaticAssignedDriver());
    }

    #[Test]
    public function it_returns_unknown_for_missing_display_name(): void
    {
        $vehicle = new Vehicle;

        $this->assertSame('Unknown', $vehicle->getDisplayName());
    }
}
