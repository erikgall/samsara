<?php

namespace Samsara\Data\HoursOfService;

use Samsara\Data\Entity;
use Samsara\Enums\DutyStatus;

/**
 * HOS clocks and driver context returned by the clocks endpoint.
 *
 * @property-read array{id?: string, name?: string}|null $driver
 * @property-read array<string, mixed>|null $currentVehicle
 * @property-read array<string, mixed>|null $currentDutyStatus
 * @property-read array<string, mixed>|null $clocks
 * @property-read array{cycleViolationDurationMs?: float|int, shiftDrivingViolationDurationMs?: float|int}|null $violations
 */
class HosClocksForDriver extends Entity
{
    /**
     * Get the driver's typed clock values.
     */
    public function getClocks(): HosClock
    {
        $clocks = $this->get('clocks');

        return HosClock::make(is_array($clocks) ? $clocks : []);
    }

    /**
     * Get the current duty status as an enum.
     */
    public function getCurrentDutyStatus(): ?DutyStatus
    {
        $currentDutyStatus = $this->get('currentDutyStatus');

        if (! is_array($currentDutyStatus)) {
            return null;
        }

        $status = $currentDutyStatus['hosStatusType'] ?? null;

        return is_string($status) && $status !== '' ? DutyStatus::tryFrom($status) : null;
    }

    /**
     * Get the cycle violation duration in hours.
     */
    public function getCycleViolationHours(): ?float
    {
        return $this->violationHours('cycleViolationDurationMs');
    }

    /**
     * Get the shift-driving violation duration in hours.
     */
    public function getShiftDrivingViolationHours(): ?float
    {
        return $this->violationHours('shiftDrivingViolationDurationMs');
    }

    private function violationHours(string $key): ?float
    {
        $violations = $this->get('violations');

        if (! is_array($violations)) {
            return null;
        }

        $milliseconds = $violations[$key] ?? null;

        return is_int($milliseconds) || is_float($milliseconds)
            ? $milliseconds / 3600000
            : null;
    }
}
