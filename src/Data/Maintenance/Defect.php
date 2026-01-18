<?php

namespace ErikGall\Samsara\Data\Maintenance;

use ErikGall\Samsara\Data\Entity;

/**
 * Defect entity.
 *
 * Represents a DVIR defect.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Defect ID
 * @property-read string|null $defectType Type of defect
 * @property-read string|null $comment Comment on the defect
 * @property-read bool|null $isResolved Whether the defect is resolved
 * @property-read string|null $createdAtTime Creation time (RFC 3339)
 * @property-read string|null $resolvedAtTime Resolution time (RFC 3339)
 * @property-read string|null $mechanicNotes Mechanic notes
 * @property-read string|null $mechanicNotesUpdatedAtTime Time mechanic notes were updated (RFC 3339)
 * @property-read array{id?: string, name?: string}|null $vehicle Vehicle information
 * @property-read array{id?: string, name?: string}|null $trailer Trailer information
 * @property-read array{id?: string, name?: string}|null $resolvedBy User who resolved the defect
 */
class Defect extends Entity
{
    /**
     * Check if the defect is resolved.
     */
    public function isResolved(): bool
    {
        return $this->isResolved ?? false;
    }
}
