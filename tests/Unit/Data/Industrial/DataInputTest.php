<?php

namespace ErikGall\Samsara\Tests\Unit\Data\Industrial;

use ErikGall\Samsara\Data\Entity;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Data\Industrial\DataInput;

/**
 * Unit tests for the DataInput entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class DataInputTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $input = new DataInput([
            'id'   => '12345',
            'name' => 'Temperature Sensor',
        ]);

        $this->assertSame('12345', $input->id);
        $this->assertSame('Temperature Sensor', $input->name);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $input = DataInput::make([
            'id' => '12345',
        ]);

        $this->assertInstanceOf(DataInput::class, $input);
        $this->assertSame('12345', $input->getId());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'id'   => '12345',
            'name' => 'Temperature Sensor',
        ];

        $input = new DataInput($data);

        $this->assertSame($data, $input->toArray());
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $input = new DataInput;

        $this->assertInstanceOf(Entity::class, $input);
    }
}
