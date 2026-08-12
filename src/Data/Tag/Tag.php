<?php

namespace Samsara\Data\Tag;

use Samsara\Data\Entity;

/**
 * Tag entity.
 *
 * Represents a Samsara tag for grouping resources.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Tag ID
 * @property-read string|null $name Tag name
 * @property-read string|null $parentTagId Parent tag ID if hierarchical
 * @property-read array{id?: string, name?: string}|null $parentTag Parent tag if hierarchical
 * @property-read array<string, string>|null $externalIds External ID mappings
 */
class Tag extends Entity
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
     * Get the parent tag as an entity.
     */
    public function getParent(): ?Tag
    {
        $parentTag = $this->get('parentTag');

        if (! is_array($parentTag) || $parentTag === []) {
            return null;
        }

        return Tag::make($parentTag);
    }

    /**
     * Check if this tag has a parent.
     */
    public function hasParent(): bool
    {
        $parentTagId = $this->get('parentTagId');

        if (is_string($parentTagId) && $parentTagId !== '') {
            return true;
        }

        $parentTag = $this->get('parentTag');

        return is_array($parentTag) && $parentTag !== [];
    }
}
