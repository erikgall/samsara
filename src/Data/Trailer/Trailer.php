<?php

namespace Samsara\Data\Trailer;

use Samsara\Data\Entity;
use Samsara\Data\Vehicle\Gateway;

/**
 * Trailer entity.
 *
 * Represents a Samsara trailer.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Trailer ID
 * @property-read string|null $name Trailer name
 * @property-read string|null $trailerSerialNumber Trailer serial number
 * @property-read string|null $licensePlate License plate
 * @property-read string|null $notes Notes about the trailer
 * @property-read bool|null $enabledForMobile Whether enabled for mobile
 * @property-read array{serial?: string, model?: string}|null $installedGateway Installed trailer gateway
 * @property-read array<string, string>|null $externalIds External ID mappings
 * @property-read array<int, array<string, mixed>>|null $attributes Trailer attributes returned by item endpoints
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
