<?php

namespace ErikGall\Samsara\Data\Attribute;

use ErikGall\Samsara\Data\Entity;

/**
 * Attribute entity.
 *
 * Represents a custom attribute definition.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Attribute ID
 * @property-read string|null $name Attribute name
 * @property-read string|null $entityType Entity type (vehicle, driver, etc.)
 * @property-read string|null $attributeType Attribute type (string, number, etc.)
 * @property-read array<int, array{id?: string, value?: string}>|null $values Attribute values
 */
class Attribute extends Entity
{
    /**
     * Check if the attribute is for drivers.
     */
    public function isForDrivers(): bool
    {
        return $this->entityType === 'driver';
    }

    /**
     * Check if the attribute is for vehicles.
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
     */
    public function isStringType(): bool
    {
        return $this->attributeType === 'string';
    }
}
