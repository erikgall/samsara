<?php

namespace ErikGall\Samsara\Data\Equipment;

use ErikGall\Samsara\Data\Entity;

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
 * @property-read int|null $headingDegrees Heading in degrees (0-360)
 * @property-read float|null $speedMilesPerHour Speed in miles per hour
 * @property-read array{id?: string, formattedAddress?: string}|null $address Address information
 * @property-read array{formattedLocation?: string}|null $reverseGeo Reverse geocoded location
 */
class EquipmentLocation extends Entity
{
    /**
     * Get the formatted address if available.
     */
    public function getFormattedAddress(): ?string
    {
        return $this->address['formattedAddress'] ?? null;
    }

    /**
     * Get the reverse geocoded location string.
     */
    public function getReverseGeoLocation(): ?string
    {
        return $this->reverseGeo['formattedLocation'] ?? null;
    }

    /**
     * Get speed in kilometers per hour.
     */
    public function getSpeedKilometersPerHour(): ?float
    {
        $mph = $this->speedMilesPerHour;

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
        $speed = $this->speedMilesPerHour;

        return $speed !== null && $speed > 0;
    }
}
