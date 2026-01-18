<?php

namespace ErikGall\Samsara\Data\Vehicle;

use ErikGall\Samsara\Data\Entity;

/**
 * GPS location entity.
 *
 * Represents a GPS location point for a vehicle.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read float|null $latitude Latitude coordinate
 * @property-read float|null $longitude Longitude coordinate
 * @property-read int|null $headingDegrees Heading in degrees (0-359)
 * @property-read float|null $speedMilesPerHour Speed in miles per hour
 * @property-read string|null $time Timestamp of the location reading
 * @property-read string|null $address Formatted address
 * @property-read bool|null $isEcuSpeed Whether speed is from ECU
 * @property-read array<string, mixed>|null $reverseGeo Reverse geocoded data
 * @property-read array<string, mixed>|null $decorations Additional decorations
 */
class GpsLocation extends Entity
{
    /**
     * Miles to kilometers conversion factor.
     */
    protected const MILES_TO_KM = 1.60934;

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
     * Get the speed in kilometers per hour.
     */
    public function getSpeedKilometersPerHour(): ?float
    {
        if ($this->speedMilesPerHour === null) {
            return null;
        }

        return $this->speedMilesPerHour * self::MILES_TO_KM;
    }
}
