<?php

namespace Samsara\Data\Vehicle;

use Samsara\Data\Entity;

/**
 * Vehicle time-series stat reading.
 *
 * @property-read mixed $value Stat value
 * @property-read string|null $time Reading time
 * @property-read array<string, array<string, mixed>>|null $decorations Requested related stat values
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class VehicleStatValue extends Entity
{
    /**
     * Get a complete decoration object by key.
     *
     * @return array<string, mixed>|null
     */
    public function getDecoration(string $key): ?array
    {
        $decorations = $this->get('decorations');

        if (! is_array($decorations)) {
            return null;
        }

        $decoration = $decorations[$key] ?? null;

        return is_array($decoration) ? $decoration : null;
    }
}
