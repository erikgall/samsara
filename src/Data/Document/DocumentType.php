<?php

namespace Samsara\Data\Document;

use Samsara\Data\Entity;

/**
 * DocumentType entity.
 *
 * Represents a document type definition.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Document type ID
 * @property-read string|null $uid Document type UID
 * @property-read string|null $name Document type name
 * @property-read string|null $orgId Organization ID
 * @property-read bool|null $isDriverEditable Whether drivers can edit
 * @property-read bool|null $requireDriverSignature Whether driver signature is required
 * @property-read array<int, array{name?: string, type?: string}>|null $fieldDefinitions Field definitions
 * @property-read array<int, array<string, mixed>>|null $conditionSets Condition sets
 */
class DocumentType extends Entity
{
    /**
     * Check if the document type is driver editable.
     */
    public function isDriverEditable(): bool
    {
        return $this->get('isDriverEditable') === true;
    }

    /**
     * Check if the document type requires a driver signature.
     */
    public function requiresSignature(): bool
    {
        return $this->get('requireDriverSignature') === true;
    }
}
