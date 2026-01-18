<?php

namespace ErikGall\Samsara\Data\Vehicle;

use ErikGall\Samsara\Data\Entity;

/**
 * Gateway entity.
 *
 * Represents a Samsara gateway device attached to a vehicle.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $serial Gateway serial number
 * @property-read string|null $model Gateway model
 */
class Gateway extends Entity {}
