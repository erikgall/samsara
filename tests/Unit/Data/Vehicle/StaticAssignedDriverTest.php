<?php

namespace ErikGall\Samsara\Tests\Unit\Data\Vehicle;

use ErikGall\Samsara\Data\Entity;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Data\Vehicle\StaticAssignedDriver;

/**
 * Unit tests for the StaticAssignedDriver entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class StaticAssignedDriverTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $driver = new StaticAssignedDriver([
            'id'   => '88668',
            'name' => 'Susan Bob',
        ]);

        $this->assertSame('88668', $driver->id);
        $this->assertSame('Susan Bob', $driver->name);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $driver = StaticAssignedDriver::make([
            'id' => '88668',
        ]);

        $this->assertInstanceOf(StaticAssignedDriver::class, $driver);
        $this->assertSame('88668', $driver->getId());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'id'   => '88668',
            'name' => 'Susan Bob',
        ];

        $driver = new StaticAssignedDriver($data);

        $this->assertSame($data, $driver->toArray());
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $driver = new StaticAssignedDriver;

        $this->assertInstanceOf(Entity::class, $driver);
    }
}
