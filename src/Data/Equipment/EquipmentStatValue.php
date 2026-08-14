<?php

namespace Samsara\Data\Equipment;

use Samsara\Data\Entity;

/**
 * Equipment time-series stat reading.
 *
 * @property-read mixed $value Stat value
 * @property-read string|null $time Reading time
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class EquipmentStatValue extends Entity {}
