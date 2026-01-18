<?php

namespace Samsara\Data\Document;

use Samsara\Data\Entity;

/**
 * Document entity.
 *
 * Represents a fleet document.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Document ID
 * @property-read string|null $name Document name
 * @property-read string|null $state Document state (pending, approved, rejected, required)
 * @property-read string|null $notes Document notes
 * @property-read string|null $documentTypeId Document type ID
 * @property-read string|null $documentTypeUid Document type UID
 * @property-read array{id?: string, name?: string}|null $driver Associated driver
 * @property-read array{id?: string, name?: string}|null $vehicle Associated vehicle
 * @property-read array<int, array{name?: string, value?: string}>|null $fields Document fields
 * @property-read string|null $createdAtTime Creation time (RFC 3339)
 * @property-read string|null $updatedAtTime Last update time (RFC 3339)
 * @property-read string|null $submittedAtUtc Submission time (UTC)
 */
class Document extends Entity
{
    /**
     * Check if the document is approved.
     */
    public function isApproved(): bool
    {
        return $this->state === 'approved';
    }

    /**
     * Check if the document is pending.
     */
    public function isPending(): bool
    {
        return $this->state === 'pending';
    }

    /**
     * Check if the document is rejected.
     */
    public function isRejected(): bool
    {
        return $this->state === 'rejected';
    }

    /**
     * Check if the document is required.
     */
    public function isRequired(): bool
    {
        return $this->state === 'required';
    }
}
