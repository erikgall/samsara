<?php

namespace Samsara\Data\Attribute;

use Samsara\Data\Entity;
use Samsara\Data\EntityCollection;

/**
 * Attribute entity.
 *
 * Represents a custom attribute definition.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Attribute ID
 * @property-read string|null $name Attribute name
 * @property-read string|null $entityType Entity type (driver or asset)
 * @property-read string|null $attributeType Attribute type (single-select, multi-select, text, freeform-multi-select, number, or date)
 * @property-read array<int, float>|null $numberValues Numeric attribute values
 * @property-read array<int, string>|null $stringValues String attribute values
 * @property-read string|null $unit Numeric attribute unit
 * @property-read array<int, array{id?: string, stringValue?: string}>|null $values Attribute values
 */
class Attribute extends Entity
{
    /**
     * Get the attribute's typed values.
     *
     * @return EntityCollection<int, AttributeValue>
     */
    public function getValues(): EntityCollection
    {
        $values = $this->get('values', []);

        if (! is_array($values)) {
            return new EntityCollection;
        }

        return new EntityCollection(array_map(
            fn (array $value): AttributeValue => AttributeValue::make($value),
            array_values(array_filter($values, 'is_array'))
        ));
    }

    /**
     * Check if the attribute is for assets.
     */
    public function isForAssets(): bool
    {
        return $this->entityType === 'asset';
    }

    /**
     * Check if the attribute is for drivers.
     */
    public function isForDrivers(): bool
    {
        return $this->entityType === 'driver';
    }

    /**
     * Check if the attribute is for vehicles.
     *
     * @deprecated The current API uses asset rather than vehicle.
     */
    public function isForVehicles(): bool
    {
        return $this->entityType === 'vehicle';
    }

    /**
     * Check if the attribute is a number type.
     */
    public function isNumberType(): bool
    {
        return $this->attributeType === 'number';
    }

    /**
     * Check if the attribute is a string type.
     *
     * @deprecated The current API uses text rather than string.
     */
    public function isStringType(): bool
    {
        return $this->attributeType === 'string';
    }

    /**
     * Check if the attribute is a text type.
     */
    public function isTextType(): bool
    {
        return $this->attributeType === 'text';
    }
}
