<?php

namespace Samsara\Data\Attribute;

use Samsara\Data\Entity;

/**
 * AttributeValue entity.
 *
 * Represents a custom attribute value.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Attribute value ID
 * @property-read string|null $attributeId Parent attribute ID
 * @property-read string|null $value Value
 * @property-read string|null $stringValue String value
 * @property-read float|int|null $numberValue Number value
 */
class AttributeValue extends Entity
{
    /**
     * Get the value (string or number).
     */
    public function getValue(): mixed
    {
        if ($this->stringValue !== null) {
            return $this->stringValue;
        }

        return $this->numberValue;
    }
}
