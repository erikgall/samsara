<?php

namespace ErikGall\Samsara\Tests\Unit\Data\Industrial;

use ErikGall\Samsara\Data\Entity;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Data\Industrial\VisionRun;

/**
 * Unit tests for the VisionRun entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class VisionRunTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $run = new VisionRun([
            'id'        => '12345',
            'startTime' => '2024-04-10T07:06:25Z',
            'endTime'   => '2024-04-10T08:00:00Z',
        ]);

        $this->assertSame('12345', $run->id);
        $this->assertSame('2024-04-10T07:06:25Z', $run->startTime);
        $this->assertSame('2024-04-10T08:00:00Z', $run->endTime);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $run = VisionRun::make([
            'id' => '12345',
        ]);

        $this->assertInstanceOf(VisionRun::class, $run);
        $this->assertSame('12345', $run->getId());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'id'        => '12345',
            'startTime' => '2024-04-10T07:06:25Z',
        ];

        $run = new VisionRun($data);

        $this->assertSame($data, $run->toArray());
    }

    #[Test]
    public function it_can_have_program(): void
    {
        $run = new VisionRun([
            'programId' => 'prog-1',
        ]);

        $this->assertSame('prog-1', $run->programId);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $run = new VisionRun;

        $this->assertInstanceOf(Entity::class, $run);
    }
}
