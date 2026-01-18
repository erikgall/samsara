<?php

namespace ErikGall\Samsara\Tests\Unit\Data\Driver;

use ErikGall\Samsara\Data\Entity;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Data\Driver\StaticAssignedVehicle;

/**
 * Unit tests for the StaticAssignedVehicle entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class StaticAssignedVehicleTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $vehicle = new StaticAssignedVehicle([
            'id'   => '123456789',
            'name' => 'Midwest Truck #4',
        ]);

        $this->assertSame('123456789', $vehicle->id);
        $this->assertSame('Midwest Truck #4', $vehicle->name);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $vehicle = StaticAssignedVehicle::make([
            'id' => '123456789',
        ]);

        $this->assertInstanceOf(StaticAssignedVehicle::class, $vehicle);
        $this->assertSame('123456789', $vehicle->getId());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'id'   => '123456789',
            'name' => 'Midwest Truck #4',
        ];

        $vehicle = new StaticAssignedVehicle($data);

        $this->assertSame($data, $vehicle->toArray());
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $vehicle = new StaticAssignedVehicle;

        $this->assertInstanceOf(Entity::class, $vehicle);
    }
}
