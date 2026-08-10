<?php

namespace Samsara\Data\Equipment;

use Samsara\Data\Entity;
use Samsara\Data\EntityCollection;

/**
 * Equipment location feed or history result.
 *
 * @property-read string|null $id Equipment ID
 * @property-read string|null $name Equipment name
 * @property-read array<int, array{latitude: float, longitude: float, time: string, heading?: float, speed?: float}>|null $locations Location readings
 */
class EquipmentLocationSeries extends Entity
{
    /**
     * Get the equipment location readings.
     *
     * @return EntityCollection<int, EquipmentLocation>
     */
    public function getLocations(): EntityCollection
    {
        $locations = $this->get('locations', []);

        if (! is_array($locations)) {
            return new EntityCollection;
        }

        return new EntityCollection(array_map(
            fn (array $location): EquipmentLocation => EquipmentLocation::make($location),
            $locations
        ));
    }
}
