<?php

namespace Samsara\Data\Industrial;

use Samsara\Data\Entity;

/**
 * IndustrialAsset entity.
 *
 * Represents an industrial asset.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Industrial asset ID
 * @property-read string|null $name Asset name
 * @property-read array<string, mixed>|null $customMetadata Custom metadata
 * @property-read array<int, array<string, mixed>>|null $dataOutputs Configured data outputs
 * @property-read bool|null $isRunning Whether the asset is running
 * @property-read array{latitude?: float, longitude?: float}|null $location Asset location
 * @property-read array<string, mixed>|null $locationDataInput Location data input
 * @property-read string|null $locationType Location type
 * @property-read array<string, mixed>|null $parentAsset Parent asset
 * @property-read array<string, mixed>|null $runningStatusDataInput Running-status data input
 * @property-read array<int, array{id?: string, name?: string}>|null $tags Tags
 */
class IndustrialAsset extends Entity
{
    /**
     * Get the asset coordinates.
     *
     * @return array{latitude: float|null, longitude: float|null}
     */
    public function getCoordinates(): array
    {
        $location = $this->get('location');

        return [
            'latitude' => is_array($location) && is_numeric($location['latitude'] ?? null)
                ? (float) $location['latitude']
                : null,
            'longitude' => is_array($location) && is_numeric($location['longitude'] ?? null)
                ? (float) $location['longitude']
                : null,
        ];
    }

    /**
     * Get the IDs of tags associated with the asset.
     *
     * @return array<int, string>
     */
    public function getTagIds(): array
    {
        $tags = $this->get('tags');

        if (! is_array($tags)) {
            return [];
        }

        $tagIds = [];

        foreach ($tags as $tag) {
            if (! is_array($tag)) {
                continue;
            }

            $id = $tag['id'] ?? null;

            if (is_string($id)) {
                $tagIds[] = $id;
            }
        }

        return $tagIds;
    }

    /**
     * Check whether the asset is running.
     */
    public function isRunning(): bool
    {
        return $this->get('isRunning') === true;
    }
}
