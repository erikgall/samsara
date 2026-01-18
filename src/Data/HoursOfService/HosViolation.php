<?php

namespace ErikGall\Samsara\Data\HoursOfService;

use ErikGall\Samsara\Data\Entity;

/**
 * HosViolation entity.
 *
 * Represents an HOS violation.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $type Violation type
 * @property-read string|null $description Violation description
 * @property-read int|null $durationMs Duration of violation in milliseconds
 * @property-read string|null $violationStartTime Violation start time (RFC 3339)
 * @property-read array{id?: string, name?: string}|null $driver Driver information
 * @property-read array{date?: string, timezone?: string}|null $day Day information
 */
class HosViolation extends Entity
{
    /**
     * Milliseconds per hour constant.
     */
    protected const MS_PER_HOUR = 3600000;

    /**
     * Milliseconds per minute constant.
     */
    protected const MS_PER_MINUTE = 60000;

    /**
     * Get the duration in hours.
     */
    public function getDurationHours(): ?float
    {
        if ($this->durationMs === null) {
            return null;
        }

        return $this->durationMs / self::MS_PER_HOUR;
    }

    /**
     * Get the duration in minutes.
     */
    public function getDurationMinutes(): ?float
    {
        if ($this->durationMs === null) {
            return null;
        }

        return $this->durationMs / self::MS_PER_MINUTE;
    }

    /**
     * Check if this is a cycle hours violation.
     */
    public function isCycleHoursViolation(): bool
    {
        return $this->type === 'cycleHoursOn';
    }

    /**
     * Check if this is a rest break missed violation.
     */
    public function isRestBreakMissedViolation(): bool
    {
        return $this->type === 'restbreakMissed';
    }

    /**
     * Check if this is a shift driving hours violation.
     */
    public function isShiftDrivingHoursViolation(): bool
    {
        return $this->type === 'shiftDrivingHours';
    }

    /**
     * Check if this is a shift hours violation.
     */
    public function isShiftHoursViolation(): bool
    {
        return $this->type === 'shiftHours';
    }

    /**
     * Check if this is an unsubmitted logs violation.
     */
    public function isUnsubmittedLogsViolation(): bool
    {
        return $this->type === 'unsubmittedLogs';
    }
}
