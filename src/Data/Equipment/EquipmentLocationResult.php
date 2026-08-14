<?php

namespace Samsara\Data\Equipment;

use Samsara\Data\Entity;

/**
 * Equipment location snapshot result.
 *
 * @property-read string|null $id Equipment ID
 * @property-read string|null $name Equipment name
 * @property-read array{latitude: float, longitude: float, time: string, heading?: float, speed?: float}|null $location Location reading
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class EquipmentLocationResult extends Entity
{
    /**
     * Get the current equipment location.
     */
    public function getLocation(): ?EquipmentLocation
    {
        $location = $this->get('location');

        return is_array($location) ? EquipmentLocation::make($location) : null;
    }
}
