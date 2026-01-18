<?php

namespace Samsara\Tests\Unit\Enums;

use Samsara\Tests\TestCase;
use Samsara\Enums\EngineState;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for the EngineState enum.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class EngineStateTest extends TestCase
{
    #[Test]
    public function it_can_be_created_from_string(): void
    {
        $state = EngineState::from('On');

        $this->assertSame(EngineState::ON, $state);
    }

    #[Test]
    public function it_has_idle_case(): void
    {
        $this->assertSame('Idle', EngineState::IDLE->value);
    }

    #[Test]
    public function it_has_off_case(): void
    {
        $this->assertSame('Off', EngineState::OFF->value);
    }

    #[Test]
    public function it_has_on_case(): void
    {
        $this->assertSame('On', EngineState::ON->value);
    }
}
