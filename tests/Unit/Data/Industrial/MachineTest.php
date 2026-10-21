<?php

namespace Samsara\Tests\Unit\Data\Industrial;

use Samsara\Tests\TestCase;
use Samsara\Data\Industrial\Machine;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for the Machine entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class MachineTest extends TestCase
{
    #[Test]
    public function it_can_have_notes(): void
    {
        $machine = new Machine([
            'notes' => 'Needs regular maintenance',
        ]);

        $this->assertSame('Needs regular maintenance', $machine->notes);
    }
}
