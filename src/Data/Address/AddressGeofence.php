<?php

namespace Samsara\Data\Address;

use Samsara\Data\Entity;

/**
 * Address geofence entity.
 *
 * Represents the geofence that defines an address and its bounds.
 * This can either be a circle or a polygon, but not both.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read array{latitude?: float, longitude?: float, radiusMeters: int}|null $circle Circle geofence data
 * @property-read array{vertices: array<int, array{latitude: float, longitude: float}>}|null $polygon Polygon geofence data
 * @property-read array{showAddresses?: bool}|null $settings Geofence settings
 */
class AddressGeofence extends Entity
{
    /**
     * Get the center point of a circular geofence.
     *
     * @return array{latitude: float, longitude: float}|null The center coordinates, or null if not a circle
     */
    public function getCenter(): ?array
    {
        if (! $this->isCircle()) {
            return null;
        }

        $latitude = $this->circle['latitude'] ?? null;
        $longitude = $this->circle['longitude'] ?? null;

        if ($latitude === null || $longitude === null) {
            return null;
        }

        return [
            'latitude'  => $latitude,
            'longitude' => $longitude,
        ];
    }

    /**
     * Get the radius of the circular geofence in meters.
     *
     * @return int|null The radius in meters, or null if not a circle
     */
    public function getRadius(): ?int
    {
        if (! $this->isCircle()) {
            return null;
        }

        return $this->circle['radiusMeters'] ?? null;
    }

    /**
     * Get the vertices of a polygon geofence.
     *
     * @return array<int, array{latitude: float, longitude: float}> The polygon vertices
     */
    public function getVertices(): array
    {
        if (! $this->isPolygon()) {
            return [];
        }

        return $this->polygon['vertices'] ?? [];
    }

    /**
     * Check if this is a circular geofence.
     */
    public function isCircle(): bool
    {
        return ! empty($this->circle);
    }

    /**
     * Check if this is a polygon geofence.
     */
    public function isPolygon(): bool
    {
        return ! empty($this->polygon);
    }

    /**
     * Check if addresses should be shown in reports.
     */
    public function shouldShowAddresses(): bool
    {
        return $this->settings['showAddresses'] ?? false;
    }
}
