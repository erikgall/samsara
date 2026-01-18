<?php

namespace ErikGall\Samsara\Tests\Unit\Data\HoursOfService;

use ErikGall\Samsara\Data\Entity;
use ErikGall\Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Data\HoursOfService\HosClock;

/**
 * Unit tests for the HosClock entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class HosClockTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $clock = new HosClock([
            'break' => [
                'timeUntilBreakDurationMs' => 28800000,
            ],
            'drive' => [
                'driveRemainingDurationMs' => 39600000,
            ],
            'shift' => [
                'shiftRemainingDurationMs' => 50400000,
            ],
            'cycle' => [
                'cycleRemainingDurationMs' => 252000000,
            ],
        ]);

        $this->assertSame(28800000, $clock->break['timeUntilBreakDurationMs']);
        $this->assertSame(39600000, $clock->drive['driveRemainingDurationMs']);
        $this->assertSame(50400000, $clock->shift['shiftRemainingDurationMs']);
        $this->assertSame(252000000, $clock->cycle['cycleRemainingDurationMs']);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $clock = HosClock::make([
            'break' => ['timeUntilBreakDurationMs' => 28800000],
        ]);

        $this->assertInstanceOf(HosClock::class, $clock);
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'break' => ['timeUntilBreakDurationMs' => 28800000],
            'drive' => ['driveRemainingDurationMs' => 39600000],
        ];

        $clock = new HosClock($data);

        $this->assertSame($data, $clock->toArray());
    }

    #[Test]
    public function it_can_get_cycle_remaining_in_hours(): void
    {
        $clock = new HosClock([
            'cycle' => [
                'cycleRemainingDurationMs' => 252000000, // 70 hours
            ],
        ]);

        $this->assertSame(70.0, $clock->getCycleRemainingHours());
    }

    #[Test]
    public function it_can_get_cycle_tomorrow_in_hours(): void
    {
        $clock = new HosClock([
            'cycle' => [
                'cycleTomorrowDurationMs' => 252000000, // 70 hours
            ],
        ]);

        $this->assertSame(70.0, $clock->getCycleTomorrowHours());
    }

    #[Test]
    public function it_can_get_drive_remaining_in_hours(): void
    {
        $clock = new HosClock([
            'drive' => [
                'driveRemainingDurationMs' => 39600000, // 11 hours
            ],
        ]);

        $this->assertSame(11.0, $clock->getDriveRemainingHours());
    }

    #[Test]
    public function it_can_get_shift_remaining_in_hours(): void
    {
        $clock = new HosClock([
            'shift' => [
                'shiftRemainingDurationMs' => 50400000, // 14 hours
            ],
        ]);

        $this->assertSame(14.0, $clock->getShiftRemainingHours());
    }

    #[Test]
    public function it_can_get_time_until_break_in_hours(): void
    {
        $clock = new HosClock([
            'break' => [
                'timeUntilBreakDurationMs' => 28800000, // 8 hours
            ],
        ]);

        $this->assertSame(8.0, $clock->getTimeUntilBreakHours());
    }

    #[Test]
    public function it_can_have_cycle_started_at_time(): void
    {
        $clock = new HosClock([
            'cycle' => [
                'cycleStartedAtTime' => '2024-04-10T00:00:00Z',
            ],
        ]);

        $this->assertSame('2024-04-10T00:00:00Z', $clock->cycle['cycleStartedAtTime']);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $clock = new HosClock;

        $this->assertInstanceOf(Entity::class, $clock);
    }

    #[Test]
    public function it_returns_null_for_cycle_remaining_when_not_set(): void
    {
        $clock = new HosClock;

        $this->assertNull($clock->getCycleRemainingHours());
    }

    #[Test]
    public function it_returns_null_for_cycle_tomorrow_when_not_set(): void
    {
        $clock = new HosClock;

        $this->assertNull($clock->getCycleTomorrowHours());
    }

    #[Test]
    public function it_returns_null_for_drive_remaining_when_not_set(): void
    {
        $clock = new HosClock;

        $this->assertNull($clock->getDriveRemainingHours());
    }

    #[Test]
    public function it_returns_null_for_shift_remaining_when_not_set(): void
    {
        $clock = new HosClock;

        $this->assertNull($clock->getShiftRemainingHours());
    }

    #[Test]
    public function it_returns_null_for_time_until_break_when_not_set(): void
    {
        $clock = new HosClock;

        $this->assertNull($clock->getTimeUntilBreakHours());
    }
}
