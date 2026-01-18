<?php

namespace Samsara\Tests\Unit\Data\HoursOfService;

use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\HoursOfService\HosViolation;

/**
 * Unit tests for the HosViolation entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class HosViolationTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $violation = new HosViolation([
            'type'               => 'shiftDrivingHours',
            'description'        => 'Shift Driving Hours (US-11 hours)',
            'durationMs'         => 31970000,
            'violationStartTime' => '2024-04-10T19:08:25Z',
        ]);

        $this->assertSame('shiftDrivingHours', $violation->type);
        $this->assertSame('Shift Driving Hours (US-11 hours)', $violation->description);
        $this->assertSame(31970000, $violation->durationMs);
        $this->assertSame('2024-04-10T19:08:25Z', $violation->violationStartTime);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $violation = HosViolation::make([
            'type' => 'shiftHours',
        ]);

        $this->assertInstanceOf(HosViolation::class, $violation);
    }

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
    public function it_can_convert_to_array(): void
    {
        $data = [
            'type'       => 'shiftDrivingHours',
            'durationMs' => 31970000,
        ];

        $violation = new HosViolation($data);

        $this->assertSame($data, $violation->toArray());
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
                'date'     => '2024-04-10',
                'timezone' => 'America/Los_Angeles',
            ],
        ]);

        $this->assertSame('2024-04-10', $violation->day['date']);
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
    public function it_extends_entity(): void
    {
        $violation = new HosViolation;

        $this->assertInstanceOf(Entity::class, $violation);
    }

    #[Test]
    public function it_returns_null_duration_when_not_set(): void
    {
        $violation = new HosViolation;

        $this->assertNull($violation->getDurationHours());
        $this->assertNull($violation->getDurationMinutes());
    }
}
