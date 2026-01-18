<?php

namespace ErikGall\Samsara\Data\HoursOfService;

use ErikGall\Samsara\Data\Entity;

/**
 * HosClock entity.
 *
 * Represents HOS clock values including remaining times.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read array{timeUntilBreakDurationMs?: float|int}|null $break Break clock information
 * @property-read array{driveRemainingDurationMs?: float|int}|null $drive Drive clock information
 * @property-read array{shiftRemainingDurationMs?: float|int}|null $shift Shift clock information
 * @property-read array{cycleRemainingDurationMs?: float|int, cycleStartedAtTime?: string, cycleTomorrowDurationMs?: float|int}|null $cycle Cycle clock information
 */
class HosClock extends Entity
{
    /**
     * Milliseconds per hour constant.
     */
    protected const MS_PER_HOUR = 3600000;

    /**
     * Get cycle remaining time in hours.
     */
    public function getCycleRemainingHours(): ?float
    {
        $ms = $this->cycle['cycleRemainingDurationMs'] ?? null;

        if ($ms === null) {
            return null;
        }

        return $ms / self::MS_PER_HOUR;
    }

    /**
     * Get cycle tomorrow time in hours.
     */
    public function getCycleTomorrowHours(): ?float
    {
        $ms = $this->cycle['cycleTomorrowDurationMs'] ?? null;

        if ($ms === null) {
            return null;
        }

        return $ms / self::MS_PER_HOUR;
    }

    /**
     * Get drive remaining time in hours.
     */
    public function getDriveRemainingHours(): ?float
    {
        $ms = $this->drive['driveRemainingDurationMs'] ?? null;

        if ($ms === null) {
            return null;
        }

        return $ms / self::MS_PER_HOUR;
    }

    /**
     * Get shift remaining time in hours.
     */
    public function getShiftRemainingHours(): ?float
    {
        $ms = $this->shift['shiftRemainingDurationMs'] ?? null;

        if ($ms === null) {
            return null;
        }

        return $ms / self::MS_PER_HOUR;
    }

    /**
     * Get time until required break in hours.
     */
    public function getTimeUntilBreakHours(): ?float
    {
        $ms = $this->break['timeUntilBreakDurationMs'] ?? null;

        if ($ms === null) {
            return null;
        }

        return $ms / self::MS_PER_HOUR;
    }
}
