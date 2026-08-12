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
 * @property-read array<string, mixed>|null $formTemplate Form template reference
 * @property-read string|null $status Submission status (notStarted, completed, archived, inProgress, needsReview, changesRequested, approved)
 * @property-read array<string, mixed>|null $approvalDetails Approval details
 * @property-read array<string, mixed>|null $asset Associated asset
 * @property-read array<string, mixed>|null $assignedTo Assigned user or driver
 * @property-read array<int, array<string, mixed>>|null $fields Form fields
 * @property-read bool|null $isRequired Whether completion is required
 * @property-read string|null $title Submission title
 * @property-read string|null $createdAtTime Creation time (RFC 3339)
 * @property-read string|null $updatedAtTime Last update time (RFC 3339)
 * @property-read string|null $submittedAtTime Submission time (RFC 3339)
 */
class FormSubmission extends Entity
{
    /**
     * Check if the submission has changes requested.
     */
    public function hasChangesRequested(): bool
    {
        return $this->status === 'changesRequested';
    }

    /**
     * Check if the submission is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if the submission is archived.
     */
    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }

    /**
     * Check if the submission is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the submission is a draft.
     *
     * @deprecated The current API does not expose a draft status.
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if the submission is in progress.
     */
    public function isInProgress(): bool
    {
        return $this->status === 'inProgress';
    }

    /**
     * Check if the submission has not been started.
     */
    public function isNotStarted(): bool
    {
        return $this->status === 'notStarted';
    }

    /**
     * Check if the submission is submitted.
     *
     * @deprecated The current API does not expose a submitted status.
     */
    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    /**
     * Check if the submission needs review.
     */
    public function needsReview(): bool
    {
        return $this->status === 'needsReview';
    }
}
