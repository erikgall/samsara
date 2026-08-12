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
 * @property-read string|null $name Document type name
 * @property-read int|null $orgId Organization ID
 * @property-read array<int, array<string, mixed>>|null $conditionalFieldSections Conditional field sections
 * @property-read array<int, array{fieldType: string, label: string, requiredField: bool}>|null $fieldTypes Field definitions
 */
class DocumentType extends Entity
{
    /**
     * Get the field definitions that are required.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getRequiredFieldTypes(): array
    {
        $fieldTypes = $this->get('fieldTypes', []);

        if (! is_array($fieldTypes)) {
            return [];
        }

        return array_values(array_filter(
            $fieldTypes,
            fn (mixed $fieldType): bool => is_array($fieldType)
                && ($fieldType['requiredField'] ?? null) === true
        ));
    }

    /**
     * Check if the document type is driver editable.
     *
     * @deprecated The current API does not expose an isDriverEditable field.
     */
    public function isDriverEditable(): bool
    {
        return $this->get('isDriverEditable') === true;
    }

    /**
     * Check if the document type requires a driver signature.
     *
     * @deprecated Inspect the current fieldTypes definitions instead.
     */
    public function requiresSignature(): bool
    {
        return $this->get('requireDriverSignature') === true;
    }
}
