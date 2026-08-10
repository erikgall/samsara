<?php

namespace Samsara\Data\Vehicle;

use Samsara\Data\Entity;

/**
 * Vehicle location snapshot result.
 *
 * @property-read string|null $id Vehicle ID
 * @property-read string|null $name Vehicle name
 * @property-read array<string, mixed>|null $location Current location
 */
class VehicleLocationResult extends Entity
{
    /**
     * Get the current location reading.
     */
    public function getLocation(): ?VehicleLocation
    {
        $location = $this->get('location');

        return is_array($location) ? VehicleLocation::make($location) : null;
    }
}
