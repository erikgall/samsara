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
 * @property-read array{id: string, externalIds?: array<string, string>}|null $asset Asset info
 * @property-read string|null $happenedAtTime Event timestamp (RFC 3339)
 * @property-read array{accuracyMeters?: float, address?: array<string, mixed>, geofence?: array<string, mixed>, headingDegrees?: int, latitude?: float, longitude?: float}|null $location Current location
 * @property-read array{ecuSpeedMetersPerSecond?: float, gpsSpeedMetersPerSecond?: float}|null $speed Speed readings by source
 */
class AssetLocation extends Entity
{
    /**
     * Get the associated asset ID.
     */
    public function getAssetId(): ?string
    {
        $asset = $this->get('asset');

        if (! is_array($asset) || ! isset($asset['id'])) {
            return null;
        }

        return (string) $asset['id'];
    }

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

    /**
     * Get the ECU speed in meters per second.
     */
    public function getEcuSpeedMetersPerSecond(): ?float
    {
        return $this->speedValue('ecuSpeedMetersPerSecond');
    }

    /**
     * Get the GPS speed in meters per second.
     */
    public function getGpsSpeedMetersPerSecond(): ?float
    {
        return $this->speedValue('gpsSpeedMetersPerSecond');
    }

    /**
     * Get the heading in degrees.
     */
    public function getHeadingDegrees(): ?int
    {
        $location = $this->get('location');

        if (! is_array($location)) {
            return null;
        }

        $heading = $location['headingDegrees'] ?? null;

        return is_int($heading) ? $heading : null;
    }

    private function speedValue(string $key): ?float
    {
        $speed = $this->get('speed');

        if (! is_array($speed)) {
            return null;
        }

        $value = $speed[$key] ?? null;

        return is_int($value) || is_float($value) ? (float) $value : null;
    }
}
