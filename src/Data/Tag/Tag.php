<?php

namespace ErikGall\Samsara\Data\Tag;

use ErikGall\Samsara\Data\Entity;

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
 * @property-read array<string, string>|null $externalIds External ID mappings
 */
class Tag extends Entity
{
    /**
     * Check if this tag has a parent.
     */
    public function hasParent(): bool
    {
        return ! empty($this->parentTagId);
    }
}
