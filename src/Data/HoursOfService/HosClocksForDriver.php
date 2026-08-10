<?php

namespace Samsara\Data\HoursOfService;

use Samsara\Data\Entity;

/**
 * HOS clocks and driver context returned by the clocks endpoint.
 *
 * @property-read array{id?: string, name?: string}|null $driver
 * @property-read array<string, mixed>|null $currentVehicle
 * @property-read array<string, mixed>|null $currentDutyStatus
 * @property-read array<string, mixed>|null $clocks
 * @property-read array<int, array<string, mixed>>|null $violations
 */
class HosClocksForDriver extends Entity
{
    /**
     * Get the driver's typed clock values.
     */
    public function getClocks(): HosClock
    {
        return HosClock::make($this->get('clocks', []));
    }
}
