<?php

namespace Samsara\Tests\Unit\Data\HoursOfService;

use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\HoursOfService\HosViolation;
use Samsara\Data\HoursOfService\HosViolationGroup;

/**
 * Unit tests for the HosViolation entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class HosViolationTest extends TestCase
{
    #[Test]
    public function it_can_check_for_cycle_hours_violation(): void
    {
        $violation = new HosViolation([
            'type' => 'cycleHoursOn',
        ]);

        $this->assertTrue($violation->isCycleHoursViolation());
    }

    #[Test]
    public function it_can_check_for_rest_break_missed_violation(): void
    {
        $violation = new HosViolation([
            'type' => 'restbreakMissed',
        ]);

        $this->assertTrue($violation->isRestBreakMissedViolation());
    }

    #[Test]
    public function it_can_check_for_shift_driving_hours_violation(): void
    {
        $violation = new HosViolation([
            'type' => 'shiftDrivingHours',
        ]);

        $this->assertTrue($violation->isShiftDrivingHoursViolation());
    }

    #[Test]
    public function it_can_check_for_shift_hours_violation(): void
    {
        $violation = new HosViolation([
            'type' => 'shiftHours',
        ]);

        $this->assertTrue($violation->isShiftHoursViolation());
    }

    #[Test]
    public function it_can_check_for_unsubmitted_logs_violation(): void
    {
        $violation = new HosViolation([
            'type' => 'unsubmittedLogs',
        ]);

        $this->assertTrue($violation->isUnsubmittedLogsViolation());
    }

    #[Test]
    public function it_can_get_duration_in_hours(): void
    {
        $violation = new HosViolation([
            'durationMs' => 7200000, // 2 hours
        ]);

        $this->assertSame(2.0, $violation->getDurationHours());
    }

    #[Test]
    public function it_can_get_duration_in_minutes(): void
    {
        $violation = new HosViolation([
            'durationMs' => 1800000, // 30 minutes
        ]);

        $this->assertSame(30.0, $violation->getDurationMinutes());
    }

    #[Test]
    public function it_can_have_day(): void
    {
        $violation = new HosViolation([
            'day' => [
                'startTime' => '2024-04-10T00:00:00Z',
                'endTime'   => '2024-04-10T23:59:59Z',
            ],
        ]);

        $this->assertSame('2024-04-10T00:00:00Z', $violation->day['startTime']);
    }

    #[Test]
    public function it_can_have_driver(): void
    {
        $violation = new HosViolation([
            'driver' => [
                'id'   => 'driver-1',
                'name' => 'John Doe',
            ],
        ]);

        $this->assertSame('driver-1', $violation->driver['id']);
        $this->assertSame('John Doe', $violation->driver['name']);
    }

    #[Test]
    public function it_returns_null_duration_when_not_set(): void
    {
        $violation = new HosViolation;

        $this->assertNull($violation->getDurationHours());
        $this->assertNull($violation->getDurationMinutes());
    }

    #[Test]
    public function it_violation_groups_return_a_typed_or_empty_collection(): void
    {
        $group = new HosViolationGroup([
            'violations' => [['type' => 'shiftHours', 'durationMs' => 60000]],
        ]);

        $this->assertInstanceOf(HosViolation::class, $group->getViolations()->first());
        $this->assertTrue((new HosViolationGroup(['violations' => null]))->getViolations()->isEmpty());
        $this->assertTrue((new HosViolationGroup(['violations' => 'invalid']))->getViolations()->isEmpty());
    }
}
