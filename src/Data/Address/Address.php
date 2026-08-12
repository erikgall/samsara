<?php

namespace Samsara\Data\Address;

use Samsara\Data\Entity;

/**
 * Address entity.
 *
 * Represents a Samsara address/geofence location.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Address ID
 * @property-read string|null $name Address name
 * @property-read string|null $formattedAddress Full street address
 * @property-read float|null $latitude Latitude coordinate
 * @property-read float|null $longitude Longitude coordinate
 * @property-read string|null $notes Notes about the address
 * @property-read string|null $createdAtTime Creation timestamp
 * @property-read array<int, string>|null $addressTypes Address type classifications
 * @property-read array<string, string>|null $externalIds External ID mappings
 * @property-read array<int, array{id: string, firstName?: string, lastName?: string}>|null $contacts Associated contacts
 * @property-read array<int, array{id: string, name?: string, parentTagId?: string}>|null $tags Associated tags
 * @property-read array{circle?: array{latitude?: float, longitude?: float, radiusMeters: int}, polygon?: array{vertices: array<int, array{latitude: float, longitude: float}>}, settings?: array{showAddresses?: bool}}|null $geofence Geofence data
 */
class Address extends Entity
{
    /**
     * Get all contact IDs associated with this address.
     *
     * @return array<int, string>
     */
    public function getContactIds(): array
    {
        $contacts = $this->get('contacts', []);

        if (! is_array($contacts)) {
            return [];
        }

        $ids = [];

        foreach ($contacts as $contact) {
            if (is_array($contact) && isset($contact['id'])) {
                $ids[] = (string) $contact['id'];
            }
        }

        return $ids;
    }

    /**
     * Get an external ID by key.
     */
    public function getExternalId(string $key): ?string
    {
        $externalIds = $this->get('externalIds');

        if (! is_array($externalIds)) {
            return null;
        }

        $externalId = $externalIds[$key] ?? null;

        return is_string($externalId) ? $externalId : null;
    }

    /**
     * Get the geofence as an entity.
     */
    public function getGeofence(): ?AddressGeofence
    {
        $geofence = $this->get('geofence');

        if (! is_array($geofence) || $geofence === []) {
            return null;
        }

        return new AddressGeofence($geofence);
    }

    /**
     * Get all tag IDs associated with this address.
     *
     * @return array<int, string>
     */
    public function getTagIds(): array
    {
        $tags = $this->get('tags', []);

        if (! is_array($tags)) {
            return [];
        }

        $ids = [];

        foreach ($tags as $tag) {
            if (is_array($tag) && isset($tag['id'])) {
                $ids[] = (string) $tag['id'];
            }
        }

        return $ids;
    }

    /**
     * Check if this address has a specific address type.
     */
    public function hasAddressType(string $type): bool
    {
        $types = $this->addressTypes ?? [];

        return in_array($type, $types, true);
    }

    /**
     * Check if this address has a geofence defined.
     */
    public function hasGeofence(): bool
    {
        $geofence = $this->get('geofence');

        return is_array($geofence) && $geofence !== [];
    }

    /**
     * Check if this address has a circular geofence.
     */
    public function isCircleGeofence(): bool
    {
        return isset($this->geofence['circle']) && ! empty($this->geofence['circle']);
    }

    /**
     * Check if this address has a polygon geofence.
     */
    public function isPolygonGeofence(): bool
    {
        return isset($this->geofence['polygon']) && ! empty($this->geofence['polygon']);
    }

    /**
     * Check if this address is a short haul location.
     */
    public function isShortHaul(): bool
    {
        return $this->hasAddressType('shortHaul');
    }

    /**
     * Check if this address is a yard location.
     */
    public function isYard(): bool
    {
        return $this->hasAddressType('yard');
    }
}
