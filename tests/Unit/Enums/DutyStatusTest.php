<?php

namespace Samsara\Tests\Unit\Enums;

use Samsara\Tests\TestCase;
use Samsara\Enums\DutyStatus;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for the DutyStatus enum.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class DutyStatusTest extends TestCase
{
    #[Test]
    public function it_can_be_created_from_string(): void
    {
        $status = DutyStatus::from('driving');

        $this->assertSame(DutyStatus::DRIVING, $status);
    }

    #[Test]
    public function it_has_driving_case(): void
    {
        $this->assertSame('driving', DutyStatus::DRIVING->value);
    }

    #[Test]
    public function it_has_off_duty_case(): void
    {
        $this->assertSame('offDuty', DutyStatus::OFF_DUTY->value);
    }

    #[Test]
    public function it_has_on_duty_case(): void
    {
        $this->assertSame('onDuty', DutyStatus::ON_DUTY->value);
    }

    #[Test]
    public function it_has_personal_conveyance_case(): void
    {
        $this->assertSame('personalConveyance', DutyStatus::PERSONAL_CONVEYANCE->value);
    }

    #[Test]
    public function it_has_sleeper_berth_case(): void
    {
        $this->assertSame('sleeperBerth', DutyStatus::SLEEPER_BERTH->value);
    }

    #[Test]
    public function it_has_yard_move_case(): void
    {
        $this->assertSame('yardMove', DutyStatus::YARD_MOVE->value);
    }
}
