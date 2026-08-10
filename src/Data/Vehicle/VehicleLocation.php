<?php

namespace Samsara\Data\Vehicle;

use Samsara\Data\Entity;

/**
 * Vehicle location reading.
 *
 * @property-read float|null $latitude Latitude in degrees
 * @property-read float|null $longitude Longitude in degrees
 * @property-read string|null $time Reading time
 * @property-read float|null $heading Heading in degrees
 * @property-read float|null $speed Speed in miles per hour
 * @property-read array<string, mixed>|null $reverseGeo Reverse-geocoded location
 */
class VehicleLocation extends Entity
{
    /**
     * Get speed in kilometers per hour.
     */
    public function getSpeedKilometersPerHour(): ?float
    {
        return $this->speed === null ? null : $this->speed * 1.60934;
    }

    /**
     * Check whether the vehicle was moving at this reading.
     */
    public function isMoving(): bool
    {
        return $this->speed !== null && $this->speed > 0;
    }
}
