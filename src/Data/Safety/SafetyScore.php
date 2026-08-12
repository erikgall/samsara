<?php

namespace Samsara\Data\Safety;

use Samsara\Data\Entity;

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
 * @property-read array<int, array{behaviorType: string, count: int, scoreImpact: float}>|null $behaviors Behavior aggregations
 * @property-read array<int, array{speedingType: string, durationMilliseconds: int, scoreImpact: float}>|null $speeding Speeding aggregations
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
     * Get average driving speed in kilometers per hour.
     */
    public function getAverageSpeedKilometersPerHour(): ?float
    {
        if ($this->driveDistanceMeters === null
            || $this->driveTimeMilliseconds === null
            || $this->driveTimeMilliseconds <= 0) {
            return null;
        }

        return ($this->driveDistanceMeters / 1000) / ($this->driveTimeMilliseconds / self::MS_PER_HOUR);
    }

    /**
     * Get the total count for an exact behavior type.
     */
    public function getBehaviorCount(string $behaviorType): int
    {
        $behaviors = $this->get('behaviors');

        if (! is_array($behaviors)) {
            return 0;
        }

        $count = 0;

        foreach ($behaviors as $behavior) {
            if (! is_array($behavior) || ($behavior['behaviorType'] ?? null) !== $behaviorType) {
                continue;
            }

            $behaviorCount = $behavior['count'] ?? null;

            if (is_int($behaviorCount)) {
                $count += $behaviorCount;
            }
        }

        return $count;
    }

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
