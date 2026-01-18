<?php

namespace ErikGall\Samsara\Data\Trailer;

use ErikGall\Samsara\Data\Entity;

/**
 * Trailer stats entity.
 *
 * Represents telemetry/stats data for a trailer, including reefer (refrigeration) data.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Trailer ID
 * @property-read string|null $name Trailer name
 * @property-read array{value: int, time?: string}|null $gpsOdometerMeters GPS odometer reading
 * @property-read array{value: int, time?: string}|null $reeferFuelPercent Reefer fuel percentage
 * @property-read array{value: int, time?: string}|null $reeferAmbientAirTemperatureMilliC Reefer ambient air temperature
 * @property-read array{value: int, time?: string}|null $reeferReturnAirTemperatureMilliCZone1 Reefer return air temp zone 1
 * @property-read array{value: int, time?: string}|null $reeferReturnAirTemperatureMilliCZone2 Reefer return air temp zone 2
 * @property-read array{value: int, time?: string}|null $reeferReturnAirTemperatureMilliCZone3 Reefer return air temp zone 3
 * @property-read array{value: int, time?: string}|null $reeferSetPointTemperatureMilliCZone1 Reefer set point temp zone 1
 * @property-read array{value: int, time?: string}|null $reeferSetPointTemperatureMilliCZone2 Reefer set point temp zone 2
 * @property-read array{value: int, time?: string}|null $reeferSetPointTemperatureMilliCZone3 Reefer set point temp zone 3
 * @property-read array{value: int, time?: string}|null $reeferObdEngineSeconds Reefer OBD engine seconds
 * @property-read array{value: string, time?: string}|null $carrierReeferState Carrier reefer state
 * @property-read array{value: string, time?: string}|null $reeferRunMode Reefer run mode
 * @property-read array{value: string, time?: string}|null $reeferDoorStateZone1 Reefer door state zone 1
 * @property-read array{value: string, time?: string}|null $reeferDoorStateZone2 Reefer door state zone 2
 * @property-read array{value: string, time?: string}|null $reeferDoorStateZone3 Reefer door state zone 3
 * @property-read array{latitude: float, longitude: float, time: string, headingDegrees?: int, speedMilesPerHour?: float}|null $gps GPS location data
 */
class TrailerStats extends Entity
{
    /**
     * Get the carrier reefer state.
     */
    public function getCarrierReeferState(): ?string
    {
        return $this->carrierReeferState['value'] ?? null;
    }

    /**
     * Get the GPS data.
     *
     * @return array{latitude: float, longitude: float, time: string, headingDegrees?: int, speedMilesPerHour?: float}|null
     */
    public function getGps(): ?array
    {
        return $this->get('gps');
    }

    /**
     * Get the GPS odometer reading in meters.
     */
    public function getGpsOdometerMeters(): ?int
    {
        return $this->gpsOdometerMeters['value'] ?? null;
    }

    /**
     * Get the reefer ambient air temperature in milli-celsius.
     */
    public function getReeferAmbientAirTemperatureMilliC(): ?int
    {
        return $this->reeferAmbientAirTemperatureMilliC['value'] ?? null;
    }

    /**
     * Get the reefer door state for zone 1.
     */
    public function getReeferDoorStateZone1(): ?string
    {
        return $this->reeferDoorStateZone1['value'] ?? null;
    }

    /**
     * Get the reefer door state for zone 2.
     */
    public function getReeferDoorStateZone2(): ?string
    {
        return $this->reeferDoorStateZone2['value'] ?? null;
    }

    /**
     * Get the reefer door state for zone 3.
     */
    public function getReeferDoorStateZone3(): ?string
    {
        return $this->reeferDoorStateZone3['value'] ?? null;
    }

    /**
     * Get the reefer fuel percentage.
     */
    public function getReeferFuelPercent(): ?int
    {
        return $this->reeferFuelPercent['value'] ?? null;
    }

    /**
     * Get the reefer OBD engine seconds.
     */
    public function getReeferObdEngineSeconds(): ?int
    {
        return $this->reeferObdEngineSeconds['value'] ?? null;
    }

    /**
     * Get the reefer return air temperature for zone 1 in milli-celsius.
     */
    public function getReeferReturnAirTemperatureMilliCZone1(): ?int
    {
        return $this->reeferReturnAirTemperatureMilliCZone1['value'] ?? null;
    }

    /**
     * Get the reefer return air temperature for zone 2 in milli-celsius.
     */
    public function getReeferReturnAirTemperatureMilliCZone2(): ?int
    {
        return $this->reeferReturnAirTemperatureMilliCZone2['value'] ?? null;
    }

    /**
     * Get the reefer return air temperature for zone 3 in milli-celsius.
     */
    public function getReeferReturnAirTemperatureMilliCZone3(): ?int
    {
        return $this->reeferReturnAirTemperatureMilliCZone3['value'] ?? null;
    }

    /**
     * Get the reefer run mode.
     */
    public function getReeferRunMode(): ?string
    {
        return $this->reeferRunMode['value'] ?? null;
    }

    /**
     * Get the reefer set point temperature for zone 1 in milli-celsius.
     */
    public function getReeferSetPointTemperatureMilliCZone1(): ?int
    {
        return $this->reeferSetPointTemperatureMilliCZone1['value'] ?? null;
    }

    /**
     * Get the reefer set point temperature for zone 2 in milli-celsius.
     */
    public function getReeferSetPointTemperatureMilliCZone2(): ?int
    {
        return $this->reeferSetPointTemperatureMilliCZone2['value'] ?? null;
    }

    /**
     * Get the reefer set point temperature for zone 3 in milli-celsius.
     */
    public function getReeferSetPointTemperatureMilliCZone3(): ?int
    {
        return $this->reeferSetPointTemperatureMilliCZone3['value'] ?? null;
    }

    /**
     * Check if the reefer door is open for zone 1.
     */
    public function isReeferDoorOpenZone1(): bool
    {
        return $this->getReeferDoorStateZone1() === 'open';
    }

    /**
     * Check if the reefer door is open for zone 2.
     */
    public function isReeferDoorOpenZone2(): bool
    {
        return $this->getReeferDoorStateZone2() === 'open';
    }

    /**
     * Check if the reefer door is open for zone 3.
     */
    public function isReeferDoorOpenZone3(): bool
    {
        return $this->getReeferDoorStateZone3() === 'open';
    }

    /**
     * Check if the reefer is currently running.
     */
    public function isReeferRunning(): bool
    {
        return $this->getCarrierReeferState() === 'running';
    }
}
