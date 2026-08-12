<?php

namespace Samsara\Tests\Unit\Data\HoursOfService;

use Samsara\Data\Entity;
use Samsara\Tests\TestCase;
use Samsara\Enums\DutyStatus;
use PHPUnit\Framework\Attributes\Test;
use Samsara\Data\HoursOfService\HosLog;
use Samsara\Data\HoursOfService\HosLogsForDriver;
use Samsara\Data\HoursOfService\HosClocksForDriver;

/**
 * Unit tests for the HosLog entity.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class HosLogTest extends TestCase
{
    #[Test]
    public function clocks_for_a_driver_expose_current_status_and_violation_hours(): void
    {
        $clocks = new HosClocksForDriver([
            'currentDutyStatus' => ['hosStatusType' => 'sleeperBed'],
            'violations'        => [
                'cycleViolationDurationMs'        => 3600000,
                'shiftDrivingViolationDurationMs' => 0,
            ],
        ]);

        $this->assertSame(DutyStatus::SLEEPER_BERTH, $clocks->getCurrentDutyStatus());
        $this->assertSame(1.0, $clocks->getCycleViolationHours());
        $this->assertSame(0.0, $clocks->getShiftDrivingViolationHours());
        $this->assertNull((new HosClocksForDriver)->getCurrentDutyStatus());
        $this->assertSame([], (new HosClocksForDriver(['clocks' => 'invalid']))->getClocks()->toArray());
        $this->assertNull((new HosClocksForDriver(['violations' => null]))->getCycleViolationHours());
        $this->assertNull((new HosClocksForDriver(['currentDutyStatus' => ['hosStatusType' => 'unknown']]))->getCurrentDutyStatus());
    }

    #[Test]
    public function grouped_logs_return_a_typed_or_empty_collection(): void
    {
        $group = new HosLogsForDriver([
            'hosLogs' => [['hosStatusType' => 'driving', 'logStartTime' => '2026-08-12T12:00:00Z']],
        ]);

        $this->assertInstanceOf(HosLog::class, $group->getLogs()->first());
        $this->assertTrue((new HosLogsForDriver(['hosLogs' => null]))->getLogs()->isEmpty());
        $this->assertTrue((new HosLogsForDriver(['hosLogs' => 'invalid']))->getLogs()->isEmpty());
    }

    #[Test]
    public function it_can_be_created_with_attributes(): void
    {
        $log = new HosLog([
            'hosStatusType' => 'driving',
            'logStartTime'  => '2024-04-10T08:00:00Z',
            'logEndTime'    => '2024-04-10T12:00:00Z',
            'remark'        => 'Morning route',
        ]);

        $this->assertSame('driving', $log->hosStatusType);
        $this->assertSame('2024-04-10T08:00:00Z', $log->logStartTime);
        $this->assertSame('2024-04-10T12:00:00Z', $log->logEndTime);
        $this->assertSame('Morning route', $log->remark);
    }

    #[Test]
    public function it_can_be_created_with_make(): void
    {
        $log = HosLog::make([
            'hosStatusType' => 'offDuty',
            'logStartTime'  => '2024-04-10T08:00:00Z',
        ]);

        $this->assertInstanceOf(HosLog::class, $log);
    }

    #[Test]
    public function it_can_check_if_driving(): void
    {
        $log = new HosLog([
            'hosStatusType' => 'driving',
        ]);

        $this->assertTrue($log->isDriving());
        $this->assertFalse($log->isOffDuty());
        $this->assertFalse($log->isOnDuty());
        $this->assertFalse($log->isSleeperBerth());
    }

    #[Test]
    public function it_can_check_if_off_duty(): void
    {
        $log = new HosLog([
            'hosStatusType' => 'offDuty',
        ]);

        $this->assertTrue($log->isOffDuty());
        $this->assertFalse($log->isDriving());
    }

    #[Test]
    public function it_can_check_if_on_duty(): void
    {
        $log = new HosLog([
            'hosStatusType' => 'onDuty',
        ]);

        $this->assertTrue($log->isOnDuty());
        $this->assertFalse($log->isDriving());
    }

    #[Test]
    public function it_can_check_if_personal_conveyance(): void
    {
        $log = new HosLog([
            'hosStatusType' => 'personalConveyance',
        ]);

        $this->assertTrue($log->isPersonalConveyance());
        $this->assertFalse($log->isDriving());
    }

    #[Test]
    public function it_can_check_if_sleeper_berth(): void
    {
        $log = new HosLog([
            'hosStatusType' => 'sleeperBed',
        ]);

        $this->assertTrue($log->isSleeperBerth());
        $this->assertFalse($log->isDriving());
    }

    #[Test]
    public function it_can_check_if_yard_move(): void
    {
        $log = new HosLog([
            'hosStatusType' => 'yardMove',
        ]);

        $this->assertTrue($log->isYardMove());
        $this->assertFalse($log->isDriving());
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $data = [
            'hosStatusType' => 'driving',
            'logStartTime'  => '2024-04-10T08:00:00Z',
        ];

        $log = new HosLog($data);

        $this->assertSame($data, $log->toArray());
    }

    #[Test]
    public function it_can_get_duty_status_enum(): void
    {
        $log = new HosLog([
            'hosStatusType' => 'sleeperBed',
        ]);

        $status = $log->getDutyStatus();

        $this->assertInstanceOf(DutyStatus::class, $status);
        $this->assertSame(DutyStatus::SLEEPER_BERTH, $status);
    }

    #[Test]
    public function it_can_have_codrivers(): void
    {
        $log = new HosLog([
            'codrivers' => [
                ['id' => 'driver-1', 'name' => 'John Doe'],
                ['id' => 'driver-2', 'name' => 'Jane Doe'],
            ],
        ]);

        $this->assertCount(2, $log->codrivers);
    }

    #[Test]
    public function it_can_have_log_recorded_location(): void
    {
        $log = new HosLog([
            'logRecordedLocation' => [
                'latitude'  => 37.7749,
                'longitude' => -122.4194,
            ],
        ]);

        $this->assertSame(37.7749, $log->logRecordedLocation['latitude']);
        $this->assertSame(-122.4194, $log->logRecordedLocation['longitude']);
    }

    #[Test]
    public function it_can_have_vehicle(): void
    {
        $log = new HosLog([
            'vehicle' => [
                'id'   => 'vehicle-1',
                'name' => 'Truck 42',
            ],
        ]);

        $this->assertSame('vehicle-1', $log->vehicle['id']);
        $this->assertSame('Truck 42', $log->vehicle['name']);
    }

    #[Test]
    public function it_extends_entity(): void
    {
        $log = new HosLog;

        $this->assertInstanceOf(Entity::class, $log);
    }

    #[Test]
    public function it_returns_null_duty_status_when_not_set(): void
    {
        $log = new HosLog;

        $this->assertNull($log->getDutyStatus());
    }

    #[Test]
    public function it_returns_null_for_an_unknown_duty_status(): void
    {
        $this->assertNull((new HosLog(['hosStatusType' => 'unknown']))->getDutyStatus());
        $this->assertNull((new HosLog(['hosStatusType' => '']))->getDutyStatus());
    }
}
