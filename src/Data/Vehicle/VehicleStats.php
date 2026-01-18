<?php

namespace Samsara\Data\Vehicle;

use Samsara\Data\Entity;
use Samsara\Enums\EngineState;

/**
 * Vehicle stats entity.
 *
 * Represents telemetry/stats data for a vehicle.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Vehicle ID
 * @property-read string|null $name Vehicle name
 * @property-read array{latitude: float, longitude: float, headingDegrees?: int, speedMilesPerHour?: float, time: string}|null $gps GPS location data
 * @property-read array{value: string, time?: string}|null $engineState Engine state
 * @property-read array{value: int, time?: string}|null $fuelPercent Fuel percentage
 * @property-read array{value: int, time?: string}|null $obdOdometerMeters OBD odometer reading
 * @property-read array{value: int, time?: string}|null $gpsOdometerMeters GPS odometer reading
 * @property-read array{value: int, time?: string}|null $engineRpm Engine RPM
 * @property-read array{value: int, time?: string}|null $batteryMilliVolts Battery voltage
 * @property-read array{value: int, time?: string}|null $engineCoolantTemperatureMilliC Engine coolant temperature
 * @property-read array{value: int, time?: string}|null $defLevelMilliPercent DEF level
 * @property-read array{value: int, time?: string}|null $obdEngineSeconds OBD engine seconds
 */
class VehicleStats extends Entity
{
    /**
     * Get the battery voltage in millivolts.
     */
    public function getBatteryMilliVolts(): ?int
    {
        return $this->batteryMilliVolts['value'] ?? null;
    }

    /**
     * Get the DEF level in milli-percent.
     */
    public function getDefLevelMilliPercent(): ?int
    {
        return $this->defLevelMilliPercent['value'] ?? null;
    }

    /**
     * Get the engine coolant temperature in milli-celsius.
     */
    public function getEngineCoolantTemperatureMilliC(): ?int
    {
        return $this->engineCoolantTemperatureMilliC['value'] ?? null;
    }

    /**
     * Get the engine RPM.
     */
    public function getEngineRpm(): ?int
    {
        return $this->engineRpm['value'] ?? null;
    }

    /**
     * Get the engine state as an enum.
     */
    public function getEngineState(): ?EngineState
    {
        $state = $this->engineState['value'] ?? null;

        if ($state === null) {
            return null;
        }

        return EngineState::tryFrom($state);
    }

    /**
     * Get the fuel percentage.
     */
    public function getFuelPercent(): ?int
    {
        return $this->fuelPercent['value'] ?? null;
    }

    /**
     * Get the GPS location as an entity.
     */
    public function getGps(): ?GpsLocation
    {
        $gps = $this->get('gps');

        if (empty($gps)) {
            return null;
        }

        return new GpsLocation($gps);
    }

    /**
     * Get the GPS odometer reading in meters.
     */
    public function getGpsOdometerMeters(): ?int
    {
        return $this->gpsOdometerMeters['value'] ?? null;
    }

    /**
     * Get the OBD engine seconds.
     */
    public function getObdEngineSeconds(): ?int
    {
        return $this->obdEngineSeconds['value'] ?? null;
    }

    /**
     * Get the OBD odometer reading in meters.
     */
    public function getObdOdometerMeters(): ?int
    {
        return $this->obdOdometerMeters['value'] ?? null;
    }

    /**
     * Check if the engine is currently idle.
     */
    public function isEngineIdle(): bool
    {
        return $this->getEngineState() === EngineState::IDLE;
    }

    /**
     * Check if the engine is currently off.
     */
    public function isEngineOff(): bool
    {
        return $this->getEngineState() === EngineState::OFF;
    }

    /**
     * Check if the engine is currently on.
     */
    public function isEngineOn(): bool
    {
        return $this->getEngineState() === EngineState::ON;
    }
}
