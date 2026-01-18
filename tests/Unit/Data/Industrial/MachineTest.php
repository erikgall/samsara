<?php

namespace ErikGall\Samsara\Tests\Unit\Data\Industrial;

use ErikGall\Samsara\Data\Entity;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Data\Industrial\Machine;

/**
 * Unit tests for the Machine entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class MachineTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $machine = new Machine([
            'id'   => '12345',
            'name' => 'CNC Machine 1',
        ]);

        $this->assertSame('12345', $machine->id);
        $this->assertSame('CNC Machine 1', $machine->name);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $machine = Machine::make([
            'id' => '12345',
        ]);

        $this->assertInstanceOf(Machine::class, $machine);
        $this->assertSame('12345', $machine->getId());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'id'   => '12345',
            'name' => 'CNC Machine 1',
        ];

        $machine = new Machine($data);

        $this->assertSame($data, $machine->toArray());
    }

    #[Test]
    public function it_can_have_notes(): void
    {
        $machine = new Machine([
            'notes' => 'Needs regular maintenance',
        ]);

        $this->assertSame('Needs regular maintenance', $machine->notes);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $machine = new Machine;

        $this->assertInstanceOf(Entity::class, $machine);
    }
}
