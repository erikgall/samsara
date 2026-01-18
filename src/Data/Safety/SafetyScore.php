<?php

namespace ErikGall\Samsara\Data\Safety;

use ErikGall\Samsara\Data\Entity;

/**
 * SafetyScore entity.
 *
 * Represents a driver or vehicle safety score.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $driverId Driver ID
 * @property-read int|null $driverScore Driver safety score (0-100)
 * @property-read int|null $driveDistanceMeters Total drive distance in meters
 * @property-read int|null $driveTimeMilliseconds Total drive time in milliseconds
 * @property-read array<int, array{type?: string, count?: int}>|null $behaviors Behavior aggregations
 * @property-read array<int, array{type?: string, count?: int}>|null $speeding Speeding aggregations
 */
class SafetyScore extends Entity
{
    /**
     * Meters per mile conversion constant.
     */
    protected const METERS_PER_MILE = 1609.344;

    /**
     * Milliseconds per hour constant.
     */
    protected const MS_PER_HOUR = 3600000;

    /**
     * Excellent score threshold.
     */
    protected const SCORE_EXCELLENT = 85;

    /**
     * Good score threshold.
     */
    protected const SCORE_GOOD = 70;

    /**
     * Get the drive distance in kilometers.
     */
    public function getDriveDistanceKilometers(): ?float
    {
        if ($this->driveDistanceMeters === null) {
            return null;
        }

        return $this->driveDistanceMeters / 1000;
    }

    /**
     * Get the drive distance in miles.
     */
    public function getDriveDistanceMiles(): ?float
    {
        if ($this->driveDistanceMeters === null) {
            return null;
        }

        return $this->driveDistanceMeters / self::METERS_PER_MILE;
    }

    /**
     * Get the drive time in hours.
     */
    public function getDriveTimeHours(): ?float
    {
        if ($this->driveTimeMilliseconds === null) {
            return null;
        }

        return $this->driveTimeMilliseconds / self::MS_PER_HOUR;
    }

    /**
     * Get the safety score.
     */
    public function getScore(): ?int
    {
        return $this->driverScore;
    }

    /**
     * Check if the score is excellent (85+).
     */
    public function isExcellent(): bool
    {
        return $this->driverScore !== null && $this->driverScore >= self::SCORE_EXCELLENT;
    }

    /**
     * Check if the score is good (70-84).
     */
    public function isGood(): bool
    {
        return $this->driverScore !== null
            && $this->driverScore >= self::SCORE_GOOD
            && $this->driverScore < self::SCORE_EXCELLENT;
    }

    /**
     * Check if the score needs improvement (below 70).
     */
    public function needsImprovement(): bool
    {
        return $this->driverScore !== null && $this->driverScore < self::SCORE_GOOD;
    }
}
