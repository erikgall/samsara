<?php

namespace Samsara\Data\Equipment;

use Samsara\Data\Entity;
use Samsara\Data\Vehicle\Gateway;

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
        $externalIds = $this->get('externalIds');

        if (! is_array($externalIds)) {
            return null;
        }

        $externalId = $externalIds[$key] ?? null;

        return is_string($externalId) ? $externalId : null;
    }

    /**
     * Get the installed gateway as an entity.
     */
    public function getInstalledGateway(): ?Gateway
    {
        $gateway = $this->get('installedGateway');

        if (! is_array($gateway) || $gateway === []) {
            return null;
        }

        return Gateway::make($gateway);
    }

    /**
     * Get all tag IDs associated with this equipment.
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
}
