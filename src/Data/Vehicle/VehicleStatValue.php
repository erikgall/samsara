<?php

namespace Samsara\Data\Vehicle;

use Samsara\Data\Entity;

/**
 * Vehicle time-series stat reading.
 *
 * @property-read mixed $value Stat value
 * @property-read string|null $time Reading time
 * @property-read array<string, array<string, mixed>>|null $decorations Requested related stat values
 */
class VehicleStatValue extends Entity {}
