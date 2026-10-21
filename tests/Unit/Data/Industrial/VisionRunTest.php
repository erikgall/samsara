<?php

namespace Samsara\Tests\Unit\Data\Industrial;

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
    public function it_can_have_program(): void
    {
        $run = new VisionRun([
            'programId' => 1,
        ]);

        $this->assertSame(1, $run->programId);
    }
}
