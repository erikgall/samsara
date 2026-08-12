<?php

namespace Samsara\Data\Equipment;

use Samsara\Data\Vehicle\GpsLocation;

/**
 * GPS location returned by equipment stats endpoints.
 *
 * @property-read float|null $latitude GPS latitude in degrees
 * @property-read float|null $longitude GPS longitude in degrees
 * @property-read string|null $time Timestamp of the location reading
 * @property-read float|null $headingDegrees Heading in degrees
 * @property-read float|null $speedMilesPerHour Speed in miles per hour
 * @property-read array{id?: string, name?: string}|null $address Matching Samsara address
 * @property-read array{formattedLocation?: string}|null $reverseGeo Reverse geocoded data
 */
class EquipmentGpsLocation extends GpsLocation
{
    /**
     * Get the matching Samsara address name.
     */
    public function getAddressName(): ?string
    {
        $address = $this->get('address');

        if (! is_array($address)) {
            return null;
        }

        $name = $address['name'] ?? null;

        return is_string($name) ? $name : null;
    }

    /**
     * Get the reverse geocoded formatted location.
     */
    public function getReverseGeoLocation(): ?string
    {
        $reverseGeo = $this->get('reverseGeo');

        if (! is_array($reverseGeo)) {
            return null;
        }

        $formattedLocation = $reverseGeo['formattedLocation'] ?? null;

        return is_string($formattedLocation) ? $formattedLocation : null;
    }
}
