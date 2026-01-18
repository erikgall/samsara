<?php

namespace ErikGall\Samsara\Data\Trip;

use ErikGall\Samsara\Data\Entity;

/**
 * Trip entity.
 *
 * Represents a vehicle trip.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $startTime Trip start time (RFC 3339)
 * @property-read string|null $endTime Trip end time (RFC 3339)
 * @property-read int|null $distanceMeters Distance traveled in meters
 * @property-read int|null $drivingTimeMs Driving time in milliseconds
 * @property-read array{id?: string, name?: string}|null $driver Associated driver
 * @property-read array{id?: string, name?: string}|null $vehicle Associated vehicle
 * @property-read array{id?: string, name?: string}|null $asset Associated asset
 * @property-read array{latitude?: float, longitude?: float}|null $startLocation Trip start location
 * @property-read array{latitude?: float, longitude?: float}|null $endLocation Trip end location
 */
class Trip extends Entity
{
    protected const METERS_PER_MILE = 1609.344;

    protected const MS_PER_HOUR = 3600000;

    protected const MS_PER_MINUTE = 60000;

    /**
     * Get the distance in kilometers.
     */
    public function getDistanceKilometers(): ?float
    {
        $meters = $this->distanceMeters;

        if ($meters === null) {
            return null;
        }

        return $meters / 1000;
    }

    /**
     * Get the distance in miles.
     */
    public function getDistanceMiles(): ?float
    {
        $meters = $this->distanceMeters;

        if ($meters === null) {
            return null;
        }

        return $meters / self::METERS_PER_MILE;
    }

    /**
     * Get the driving time in hours.
     */
    public function getDrivingTimeHours(): ?float
    {
        $ms = $this->drivingTimeMs;

        if ($ms === null) {
            return null;
        }

        return $ms / self::MS_PER_HOUR;
    }

    /**
     * Get the driving time in minutes.
     */
    public function getDrivingTimeMinutes(): ?float
    {
        $ms = $this->drivingTimeMs;

        if ($ms === null) {
            return null;
        }

        return $ms / self::MS_PER_MINUTE;
    }
}
