<?php

namespace Samsara\Data\Asset;

use Samsara\Data\Entity;

/**
 * AssetLocation entity.
 *
 * Represents an asset's location data.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read float|null $latitude Latitude
 * @property-read float|null $longitude Longitude
 * @property-read float|null $headingDegrees Heading in degrees
 * @property-read float|null $speedMilesPerHour Speed in miles per hour
 * @property-read string|null $time Location timestamp (RFC 3339)
 * @property-read array{latitude?: float, longitude?: float}|null $location Current location
 * @property-read array{id?: string, name?: string}|null $asset Asset info
 * @property-read array{name?: string, formattedAddress?: string}|null $address Address info
 */
class AssetLocation extends Entity
{
    /**
     * Get the coordinates as an array.
     *
     * @return array{latitude: float|null, longitude: float|null}
     */
    public function getCoordinates(): array
    {
        $location = $this->get('location');

        return [
            'latitude'  => is_array($location) ? ($location['latitude'] ?? null) : $this->get('latitude'),
            'longitude' => is_array($location) ? ($location['longitude'] ?? null) : $this->get('longitude'),
        ];
    }
}
