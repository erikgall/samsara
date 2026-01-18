<?php

namespace ErikGall\Samsara\Data\Trailer;

use ErikGall\Samsara\Data\Entity;

/**
 * Trailer entity.
 *
 * Represents a Samsara trailer.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Trailer ID
 * @property-read string|null $name Trailer name
 * @property-read string|null $assetSerialNumber Asset serial number
 * @property-read string|null $licensePlate License plate
 * @property-read string|null $notes Notes about the trailer
 * @property-read bool|null $enabledForMobile Whether enabled for mobile
 * @property-read array<string, string>|null $externalIds External ID mappings
 * @property-read array<int, array{id: string, name?: string, parentTagId?: string}>|null $tags Associated tags
 */
class Trailer extends Entity
{
    /**
     * Get the display name for the trailer.
     */
    public function getDisplayName(): string
    {
        return $this->name ?? 'Unknown';
    }

    /**
     * Get an external ID by key.
     */
    public function getExternalId(string $key): ?string
    {
        $externalIds = $this->externalIds ?? [];

        return $externalIds[$key] ?? null;
    }

    /**
     * Get all tag IDs associated with this trailer.
     *
     * @return array<int, string>
     */
    public function getTagIds(): array
    {
        $tags = $this->tags ?? [];

        return array_map(fn (array $tag): string => (string) $tag['id'], $tags);
    }
}
