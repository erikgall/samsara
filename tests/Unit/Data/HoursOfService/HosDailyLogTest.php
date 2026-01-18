<?php

namespace Samsara\Tests\Unit\Data\HoursOfService;

use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\HoursOfService\HosDailyLog;

/**
 * Unit tests for the HosDailyLog entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class HosDailyLogTest extends TestCase
{
    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $dailyLog = new HosDailyLog([
            'startTime' => '2024-04-10T00:00:00Z',
            'endTime'   => '2024-04-10T23:59:59Z',
        ]);

        $this->assertSame('2024-04-10T00:00:00Z', $dailyLog->startTime);
        $this->assertSame('2024-04-10T23:59:59Z', $dailyLog->endTime);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $dailyLog = HosDailyLog::make([
            'startTime' => '2024-04-10T00:00:00Z',
            'endTime'   => '2024-04-10T23:59:59Z',
        ]);

        $this->assertInstanceOf(HosDailyLog::class, $dailyLog);
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'startTime' => '2024-04-10T00:00:00Z',
            'endTime'   => '2024-04-10T23:59:59Z',
        ];

        $dailyLog = new HosDailyLog($data);

        $this->assertSame($data, $dailyLog->toArray());
    }

    #[Test]
    public function it_can_have_distance_traveled(): void
    {
        $dailyLog = new HosDailyLog([
            'distanceTraveled' => [
                'distanceMeters' => 150000,
            ],
        ]);

        $this->assertSame(150000, $dailyLog->distanceTraveled['distanceMeters']);
    }

    #[Test]
    public function it_can_have_driver(): void
    {
        $dailyLog = new HosDailyLog([
            'driver' => [
                'id'   => 'driver-1',
                'name' => 'John Doe',
            ],
        ]);

        $this->assertSame('driver-1', $dailyLog->driver['id']);
        $this->assertSame('John Doe', $dailyLog->driver['name']);
    }

    #[Test]
    public function it_can_have_duty_status_durations(): void
    {
        $dailyLog = new HosDailyLog([
            'dutyStatusDurations' => [
                'driveDurationMs'   => 39600000, // 11 hours
                'onDutyDurationMs'  => 14400000, // 4 hours
                'offDutyDurationMs' => 28800000, // 8 hours
            ],
        ]);

        $this->assertSame(39600000, $dailyLog->dutyStatusDurations['driveDurationMs']);
        $this->assertSame(14400000, $dailyLog->dutyStatusDurations['onDutyDurationMs']);
    }

    #[Test]
    public function it_can_have_log_meta_data(): void
    {
        $dailyLog = new HosDailyLog([
            'logMetaData' => [
                'shippingId' => 'SHIP-12345',
            ],
        ]);

        $this->assertSame('SHIP-12345', $dailyLog->logMetaData['shippingId']);
    }

    #[Test]
    public function it_can_have_pending_duty_status_durations(): void
    {
        $dailyLog = new HosDailyLog([
            'pendingDutyStatusDurations' => [
                'driveDurationMs' => 1800000, // 30 minutes
            ],
        ]);

        $this->assertSame(1800000, $dailyLog->pendingDutyStatusDurations['driveDurationMs']);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $dailyLog = new HosDailyLog;

        $this->assertInstanceOf(Entity::class, $dailyLog);
    }
}
