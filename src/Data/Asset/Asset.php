<?php

namespace Samsara\Data\Asset;

use Samsara\Data\Entity;

/**
 * Asset entity.
 *
 * Represents a fleet asset.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Asset ID
 * @property-read string|null $name Asset name
 * @property-read array<int, array<string, mixed>>|null $attributes Associated attributes
 * @property-read string|null $serialNumber Serial number
 * @property-read string|null $type Asset type (uncategorized, trailer, equipment, unpowered, or vehicle)
 * @property-read string|null $licensePlate License plate
 * @property-read string|null $make Manufacturer
 * @property-read string|null $model Model
 * @property-read bool|null $readingsIngestionEnabled Whether readings ingestion is enabled
 * @property-read string|null $regulationMode Regulation mode
 * @property-read string|null $vin Vehicle identification number
 * @property-read int|null $year Model year
 * @property-read array<string, string>|null $externalIds External ID mappings
 * @property-read array<int, array{id?: string, name?: string}>|null $tags Tags
 * @property-read string|null $createdAtTime Creation time (RFC 3339)
 * @property-read string|null $updatedAtTime Last update time (RFC 3339)
 */
class Asset extends Entity
{
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
     * Get all tag IDs associated with this asset.
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
     * Check if the asset is equipment.
     */
    public function isEquipment(): bool
    {
        return $this->type === 'equipment';
    }

    /**
     * Check if the asset is a trailer.
     */
    public function isTrailer(): bool
    {
        return $this->type === 'trailer';
    }

    /**
     * Check if the asset is uncategorized.
     */
    public function isUncategorized(): bool
    {
        return $this->type === 'uncategorized';
    }

    /**
     * Check if the asset is unpowered equipment.
     */
    public function isUnpowered(): bool
    {
        return $this->type === 'unpowered';
    }

    /**
     * Check if the asset is a vehicle.
     */
    public function isVehicle(): bool
    {
        return $this->type === 'vehicle';
    }
}
