<?php

namespace Samsara\Tests\Unit\Data\Industrial;

use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\Industrial\VisionRun;

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
            'deviceId'    => 1234512345123,
            'startedAtMs' => 1553808606097,
            'endedAtMs'   => 1553808616097,
        ]);

        $this->assertSame(1234512345123, $run->deviceId);
        $this->assertSame(1553808606097, $run->startedAtMs);
        $this->assertSame(1553808616097, $run->endedAtMs);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $run = VisionRun::make([
            'cameraId' => 1234512345999,
        ]);

        $this->assertInstanceOf(VisionRun::class, $run);
        $this->assertNull($run->getId());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'deviceId'    => 1234512345123,
            'startedAtMs' => 1553808606097,
        ];

        $run = new VisionRun($data);

        $this->assertSame($data, $run->toArray());
    }

    #[Test]
    public function it_can_have_program(): void
    {
        $run = new VisionRun([
            'programId' => 1,
        ]);

        $this->assertSame(1, $run->programId);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $run = new VisionRun;

        $this->assertInstanceOf(Entity::class, $run);
    }
}
