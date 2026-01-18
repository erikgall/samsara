<?php

namespace ErikGall\Samsara\Data\Form;

use ErikGall\Samsara\Data\Entity;

/**
 * FormTemplate entity.
 *
 * Represents a form template.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Form template ID
 * @property-read string|null $name Form template name
 * @property-read string|null $description Template description
 * @property-read int|null $revision Template revision
 * @property-read array<int, array{name?: string, type?: string}>|null $fieldDefinitions Field definitions
 * @property-read string|null $createdAtTime Creation time (RFC 3339)
 * @property-read string|null $updatedAtTime Last update time (RFC 3339)
 */
class FormTemplate extends Entity {}
