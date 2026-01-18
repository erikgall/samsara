<?php

namespace Samsara\Tests\Unit\Data\Industrial;

use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\Industrial\DataPoint;

/**
 * Unit tests for the DataPoint entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class DataPointTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $point = new DataPoint([
            'time'  => '2024-04-10T07:06:25Z',
            'value' => 98.6,
        ]);

        $this->assertSame('2024-04-10T07:06:25Z', $point->time);
        $this->assertSame(98.6, $point->value);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $point = DataPoint::make([
            'time' => '2024-04-10T07:06:25Z',
        ]);

        $this->assertInstanceOf(DataPoint::class, $point);
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'time'  => '2024-04-10T07:06:25Z',
            'value' => 98.6,
        ];

        $point = new DataPoint($data);

        $this->assertSame($data, $point->toArray());
    }

    #[Test]
    public function it_can_have_data_input(): void
    {
        $point = new DataPoint([
            'dataInput' => [
                'id'   => 'input-1',
                'name' => 'Temperature',
            ],
        ]);

        $this->assertSame('input-1', $point->dataInput['id']);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $point = new DataPoint;

        $this->assertInstanceOf(Entity::class, $point);
    }
}
