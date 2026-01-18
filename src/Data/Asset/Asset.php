<?php

namespace ErikGall\Samsara\Data\Asset;

use ErikGall\Samsara\Data\Entity;

/**
 * Asset entity.
 *
 * Represents a fleet asset.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Asset ID
 * @property-read string|null $name Asset name
 * @property-read string|null $serial Serial number
 * @property-read string|null $assetType Asset type (vehicle, trailer, equipment)
 * @property-read float|null $purchasePrice Purchase price
 * @property-read array{id?: string, serial?: string}|null $gateway Gateway info
 * @property-read array{latitude?: float, longitude?: float}|null $location Current location
 * @property-read array<int, array{id?: string, name?: string}>|null $tags Tags
 * @property-read string|null $createdAtTime Creation time (RFC 3339)
 * @property-read string|null $updatedAtTime Last update time (RFC 3339)
 */
class Asset extends Entity
{
    /**
     * Check if the asset is equipment.
     */
    public function isEquipment(): bool
    {
        return $this->assetType === 'equipment';
    }

    /**
     * Check if the asset is a trailer.
     */
    public function isTrailer(): bool
    {
        return $this->assetType === 'trailer';
    }

    /**
     * Check if the asset is a vehicle.
     */
    public function isVehicle(): bool
    {
        return $this->assetType === 'vehicle';
    }
}
