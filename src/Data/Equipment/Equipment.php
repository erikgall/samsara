<?php

namespace ErikGall\Samsara\Data\Equipment;

use ErikGall\Samsara\Data\Entity;

/**
 * Equipment entity.
 *
 * Represents a Samsara equipment asset.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Equipment ID
 * @property-read string|null $name Equipment name
 * @property-read string|null $assetSerial Equipment identification number
 * @property-read string|null $notes Notes about the equipment
 * @property-read array<string, string>|null $externalIds External ID mappings
 * @property-read array<int, array{id: string, name?: string, parentTagId?: string}>|null $tags Associated tags
 * @property-read array{serial?: string, model?: string}|null $installedGateway Installed gateway data
 */
class Equipment extends Entity
{
    /**
     * Get the display name for the equipment.
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
     * Get all tag IDs associated with this equipment.
     *
     * @return array<int, string>
     */
    public function getTagIds(): array
    {
        $tags = $this->tags ?? [];

        return array_map(fn (array $tag): string => (string) $tag['id'], $tags);
    }
}
