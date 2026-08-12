<?php

namespace Samsara\Data\HoursOfService;

use Samsara\Data\Entity;

/**
 * HosDailyLog entity.
 *
 * Represents an HOS daily log.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $startTime Start time of the daily log (RFC 3339)
 * @property-read string|null $endTime End time of the daily log (RFC 3339)
 * @property-read array{id?: string, name?: string}|null $driver Driver information
 * @property-read array{driveDistanceMeters?: int, personalConveyanceDistanceMeters?: int, yardMoveDistanceMeters?: int}|null $distanceTraveled Distance traveled information
 * @property-read array{activeDurationMs?: int, driveDurationMs?: int, offDutyDurationMs?: int, onDutyDurationMs?: int, personalConveyanceDurationMs?: int, sleeperBerthDurationMs?: int, waitingTimeDurationMs?: int, yardMoveDurationMs?: int}|null $dutyStatusDurations Duty status durations
 * @property-read array{activeDurationMs?: int, driveDurationMs?: int, offDutyDurationMs?: int, onDutyDurationMs?: int, personalConveyanceDurationMs?: int, sleeperBerthDurationMs?: int, waitingTimeDurationMs?: int, yardMoveDurationMs?: int}|null $pendingDutyStatusDurations Pending duty status durations
 * @property-read array<string, mixed>|null $logMetaData Log metadata
 */
class HosDailyLog extends Entity
{
    protected const METERS_PER_MILE = 1609.344;

    protected const MS_PER_HOUR = 3600000;

    /**
     * Get the drive distance in kilometers.
     */
    public function getDriveDistanceKilometers(): ?float
    {
        $meters = $this->driveDistanceMeters();

        return $meters === null ? null : $meters / 1000;
    }

    /**
     * Get the drive distance in miles.
     */
    public function getDriveDistanceMiles(): ?float
    {
        $meters = $this->driveDistanceMeters();

        return $meters === null ? null : $meters / self::METERS_PER_MILE;
    }

    /**
     * Get the driving duration in hours.
     */
    public function getDriveTimeHours(): ?float
    {
        $durations = $this->get('dutyStatusDurations');

        if (! is_array($durations)) {
            return null;
        }

        $milliseconds = $durations['driveDurationMs'] ?? null;

        return is_int($milliseconds) ? $milliseconds / self::MS_PER_HOUR : null;
    }

    private function driveDistanceMeters(): ?int
    {
        $distance = $this->get('distanceTraveled');

        if (! is_array($distance)) {
            return null;
        }

        $meters = $distance['driveDistanceMeters'] ?? null;

        return is_int($meters) ? $meters : null;
    }
}
