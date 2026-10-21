<?php

namespace Samsara\Tests\Unit\Data\HoursOfService;

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
    public function it_can_have_distance_traveled(): void
    {
        $dailyLog = new HosDailyLog([
            'distanceTraveled' => [
                'driveDistanceMeters'              => 150000,
                'personalConveyanceDistanceMeters' => 2500,
                'yardMoveDistanceMeters'           => 500,
            ],
        ]);

        $this->assertSame(150000, $dailyLog->distanceTraveled['driveDistanceMeters']);
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
                'shippingDocs' => 'SHIP-12345',
                'isCertified'  => true,
            ],
        ]);

        $this->assertSame('SHIP-12345', $dailyLog->logMetaData['shippingDocs']);
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
    public function it_converts_drive_distance_and_time(): void
    {
        $dailyLog = new HosDailyLog([
            'distanceTraveled'    => ['driveDistanceMeters' => 160934],
            'dutyStatusDurations' => ['driveDurationMs' => 7200000],
        ]);

        $this->assertSame(160.934, $dailyLog->getDriveDistanceKilometers());
        $this->assertEqualsWithDelta(100.0, $dailyLog->getDriveDistanceMiles(), 0.001);
        $this->assertSame(2.0, $dailyLog->getDriveTimeHours());
    }

    #[Test]
    public function it_drive_conversions_preserve_zero_and_missing_values(): void
    {
        $zero = new HosDailyLog([
            'distanceTraveled'    => ['driveDistanceMeters' => 0],
            'dutyStatusDurations' => ['driveDurationMs' => 0],
        ]);

        $this->assertSame(0.0, $zero->getDriveDistanceKilometers());
        $this->assertSame(0.0, $zero->getDriveDistanceMiles());
        $this->assertSame(0.0, $zero->getDriveTimeHours());
        $this->assertNull((new HosDailyLog)->getDriveDistanceKilometers());
        $this->assertNull((new HosDailyLog(['distanceTraveled' => null]))->getDriveDistanceMiles());
        $this->assertNull((new HosDailyLog(['dutyStatusDurations' => []]))->getDriveTimeHours());
    }
}
