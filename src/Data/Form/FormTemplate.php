<?php

namespace Samsara\Data\Form;

use Samsara\Data\Entity;

/**
 * FormTemplate entity.
 *
 * Represents a form template.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Form template ID
 * @property-read string|null $title Form template title
 * @property-read string|null $description Template description
 * @property-read string|null $revisionId Template revision ID
 * @property-read string|null $formCategory Template category
 * @property-read array<int, array<string, mixed>>|null $fields Field definitions
 * @property-read array<int, array<string, mixed>>|null $sections Template sections
 * @property-read array<string, mixed>|null $approvalConfig Approval configuration
 * @property-read array<string, mixed>|null $createdBy Template creator
 * @property-read array<string, mixed>|null $updatedBy Last updater
 * @property-read string|null $createdAtTime Creation time (RFC 3339)
 * @property-read string|null $updatedAtTime Last update time (RFC 3339)
 */
class FormTemplate extends Entity {}
