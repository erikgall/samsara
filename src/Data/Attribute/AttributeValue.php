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
 * @property-read string|null $stringValue String value
 * @property-read float|int|null $numberValue Deprecated legacy numeric value not returned by the current API
 */
class AttributeValue extends Entity
{
    /**
     * Get the value (string or number).
     *
     * @deprecated Use stringValue for the current contract. The numeric fallback is retained for backwards compatibility.
     */
    public function getValue(): mixed
    {
        if ($this->stringValue !== null) {
            return $this->stringValue;
        }

        return $this->numberValue;
    }
}
