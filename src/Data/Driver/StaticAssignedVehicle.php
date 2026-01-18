<?php

namespace Samsara\Data\Driver;

use Samsara\Data\Entity;

/**
 * Static assigned vehicle entity.
 *
 * Represents a vehicle that is statically assigned to a driver.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Vehicle ID
 * @property-read string|null $name Vehicle name
 */
class StaticAssignedVehicle extends Entity {}
