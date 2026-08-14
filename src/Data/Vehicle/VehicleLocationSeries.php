<?php

namespace Samsara\Data\Vehicle;

use Samsara\Data\Entity;
use Samsara\Data\EntityCollection;

/**
 * Vehicle location feed or history result.
 *
 * @property-read string|null $id Vehicle ID
 * @property-read string|null $name Vehicle name
 * @property-read array<int, array<string, mixed>>|null $locations Location readings
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class VehicleLocationSeries extends Entity
{
    /**
     * Get the typed location readings.
     *
     * @return EntityCollection<int, VehicleLocation>
     */
    public function getLocations(): EntityCollection
    {
        $locations = $this->get('locations', []);

        if (! is_array($locations)) {
            return new EntityCollection;
        }

        return new EntityCollection(array_map(
            fn (array $location): VehicleLocation => VehicleLocation::make($location),
            $locations
        ));
    }
}
