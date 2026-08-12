<?php

namespace Samsara\Data\Equipment;

use Samsara\Data\Entity;

/**
 * Equipment location entity.
 *
 * Represents GPS location data for equipment.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read float|null $latitude GPS latitude in degrees
 * @property-read float|null $longitude GPS longitude in degrees
 * @property-read string|null $time Timestamp of the location reading
 * @property-read float|null $heading Heading in degrees
 * @property-read float|null $speed Speed in miles per hour
 */
class EquipmentLocation extends Entity
{
    /**
     * Get the coordinates as an array.
     *
     * @return array{latitude: float|null, longitude: float|null}
     */
    public function getCoordinates(): array
    {
        return [
            'latitude'  => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }

    /**
     * Get speed in kilometers per hour.
     */
    public function getSpeedKilometersPerHour(): ?float
    {
        $mph = $this->speed;

        if ($mph === null) {
            return null;
        }

        return $mph * 1.60934;
    }

    /**
     * Check if the equipment is moving.
     */
    public function isMoving(): bool
    {
        $speed = $this->speed;

        return $speed !== null && $speed > 0;
    }
}
