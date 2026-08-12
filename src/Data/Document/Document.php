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
 * @property-read string|null $state Document state (submitted, required, or archived)
 * @property-read string|null $notes Document notes
 * @property-read array<int, array<string, mixed>>|null $conditionalFieldSections Conditional field sections
 * @property-read array{id?: string, name?: string}|null $documentType Document type
 * @property-read array{id?: string, name?: string}|null $driver Associated driver
 * @property-read array{id?: string, name?: string}|null $vehicle Associated vehicle
 * @property-read array<int, array<string, mixed>>|null $fields Document fields
 * @property-read array<string, mixed>|null $route Associated route
 * @property-read array<string, mixed>|null $routeStop Associated route stop
 * @property-read string|null $createdAtTime Creation time (RFC 3339)
 * @property-read string|null $updatedAtTime Last update time (RFC 3339)
 */
class Document extends Entity
{
    /**
     * Check if the document is approved.
     *
     * @deprecated The current API does not expose an approved state.
     */
    public function isApproved(): bool
    {
        return $this->state === 'approved';
    }

    /**
     * Check if the document is archived.
     */
    public function isArchived(): bool
    {
        return $this->state === 'archived';
    }

    /**
     * Check if the document is pending.
     *
     * @deprecated The current API does not expose a pending state.
     */
    public function isPending(): bool
    {
        return $this->state === 'pending';
    }

    /**
     * Check if the document is rejected.
     *
     * @deprecated The current API does not expose a rejected state.
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

    /**
     * Check if the document is submitted.
     */
    public function isSubmitted(): bool
    {
        return $this->state === 'submitted';
    }
}
