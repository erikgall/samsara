<?php

namespace Samsara\Tests\Unit\Data\Industrial;

use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\Industrial\DataInput;
use Samsara\Data\Industrial\DataPoint;

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
    public function it_detects_singular_and_plural_data_points(): void
    {
        $this->assertTrue(DataInput::make([
            'numberPoint' => ['time' => '2026-08-10T12:00:00Z', 'value' => 98.6],
        ])->hasDataPoints());
        $this->assertTrue(DataInput::make([
            'stringPoints' => [['time' => '2026-08-10T12:00:01Z', 'value' => 'running']],
        ])->hasDataPoints());

        foreach ([
            [],
            ['numberPoint'  => null, 'stringPoints' => null],
            ['stringPoints' => []],
        ] as $attributes) {
            $this->assertFalse(DataInput::make($attributes)->hasDataPoints());
        }
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $input = new DataInput;

        $this->assertInstanceOf(Entity::class, $input);
    }

    #[Test]
    public function it_hydrates_current_singular_and_plural_data_points(): void
    {
        $input = DataInput::make([
            'id'          => 'input-1',
            'numberPoint' => [
                'time'  => '2026-08-10T12:00:00Z',
                'value' => 98.6,
            ],
            'stringPoints' => [
                ['time' => '2026-08-10T12:00:01Z', 'value' => 'running'],
            ],
        ]);

        $this->assertInstanceOf(DataPoint::class, $input->numberPoint);
        $this->assertSame(98.6, $input->numberPoint->value);
        $this->assertInstanceOf(DataPoint::class, $input->stringPoints[0]);
        $this->assertSame('running', $input->stringPoints[0]->value);
    }
}
