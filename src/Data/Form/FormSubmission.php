<?php

namespace Samsara\Data\Form;

use Samsara\Data\Entity;

/**
 * FormSubmission entity.
 *
 * Represents a form submission.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Form submission ID
 * @property-read string|null $formTemplateId Form template ID
 * @property-read string|null $status Submission status (draft, submitted)
 * @property-read array{id?: string, name?: string}|null $driver Associated driver
 * @property-read array{id?: string, name?: string}|null $vehicle Associated vehicle
 * @property-read array<int, array{name?: string, value?: string}>|null $fields Form fields
 * @property-read string|null $createdAtTime Creation time (RFC 3339)
 * @property-read string|null $updatedAtTime Last update time (RFC 3339)
 * @property-read string|null $submittedAtTime Submission time (RFC 3339)
 */
class FormSubmission extends Entity
{
    /**
     * Check if the submission is a draft.
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if the submission is submitted.
     */
    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }
}
