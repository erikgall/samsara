<?php

namespace Samsara\Data\Equipment;

use Samsara\Data\Entity;
use Samsara\Enums\EngineState;

/**
 * Equipment stats entity.
 *
 * Represents telemetry/stats data for equipment.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Equipment ID
 * @property-read string|null $name Equipment name
 * @property-read array{value: int, time?: string}|null $engineRpm Engine RPM
 * @property-read array{value: int, time?: string}|null $fuelPercent Fuel percentage
 * @property-read array{value: int, time?: string}|null $gpsOdometerMeters GPS odometer reading
 * @property-read array{value: int, time?: string}|null $gatewayEngineSeconds Gateway engine seconds
 * @property-read array{value: int, time?: string}|null $obdEngineSeconds OBD engine seconds
 * @property-read array{value: string, time?: string}|null $gatewayEngineState Gateway engine state
 * @property-read array{value: string, time?: string}|null $obdEngineState OBD engine state
 * @property-read array{value: int, time?: string}|null $engineTotalIdleTimeMinutes Total engine idle time
 * @property-read array{latitude: float, longitude: float, time: string, headingDegrees?: int, speedMilesPerHour?: float}|null $gps GPS location data
 */
class EquipmentStats extends Entity
{
    /**
     * Get the engine RPM value.
     */
    public function getEngineRpm(): ?int
    {
        return $this->engineRpm['value'] ?? null;
    }

    /**
     * Get the engine total idle time in minutes.
     */
    public function getEngineTotalIdleTimeMinutes(): ?int
    {
        return $this->engineTotalIdleTimeMinutes['value'] ?? null;
    }

    /**
     * Get the fuel percentage.
     */
    public function getFuelPercent(): ?int
    {
        return $this->fuelPercent['value'] ?? null;
    }

    /**
     * Get the gateway engine seconds.
     */
    public function getGatewayEngineSeconds(): ?int
    {
        return $this->gatewayEngineSeconds['value'] ?? null;
    }

    /**
     * Get the gateway engine state as an enum.
     */
    public function getGatewayEngineState(): ?EngineState
    {
        $state = $this->gatewayEngineState['value'] ?? null;

        if ($state === null) {
            return null;
        }

        return EngineState::tryFrom($state);
    }

    /**
     * Get the GPS location as an entity.
     */
    public function getGps(): ?EquipmentLocation
    {
        $gps = $this->get('gps');

        if (empty($gps)) {
            return null;
        }

        return new EquipmentLocation($gps);
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
     * Get the OBD engine state as an enum.
     */
    public function getObdEngineState(): ?EngineState
    {
        $state = $this->obdEngineState['value'] ?? null;

        if ($state === null) {
            return null;
        }

        return EngineState::tryFrom($state);
    }

    /**
     * Check if the gateway engine is off.
     */
    public function isGatewayEngineOff(): bool
    {
        return $this->getGatewayEngineState() === EngineState::OFF;
    }

    /**
     * Check if the gateway engine is on.
     */
    public function isGatewayEngineOn(): bool
    {
        return $this->getGatewayEngineState() === EngineState::ON;
    }

    /**
     * Check if the OBD engine is idle.
     */
    public function isObdEngineIdle(): bool
    {
        return $this->getObdEngineState() === EngineState::IDLE;
    }

    /**
     * Check if the OBD engine is off.
     */
    public function isObdEngineOff(): bool
    {
        return $this->getObdEngineState() === EngineState::OFF;
    }

    /**
     * Check if the OBD engine is on.
     */
    public function isObdEngineOn(): bool
    {
        return $this->getObdEngineState() === EngineState::ON;
    }
}
